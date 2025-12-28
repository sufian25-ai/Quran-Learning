<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Process payment with Stripe
     */
    public function processStripe(Enrollment $enrollment, string $paymentMethodId): array
    {
        try {
            // Create payment intent
            $paymentIntent = PaymentIntent::create([
                'amount' => (int) ($enrollment->amount * 100), // Convert to cents
                'currency' => strtolower($enrollment->currency),
                'payment_method' => $paymentMethodId,
                'confirm' => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
                'metadata' => [
                    'enrollment_id' => $enrollment->id,
                    'user_id' => $enrollment->user_id,
                    'course_id' => $enrollment->course_id,
                    'type' => $enrollment->type,
                ],
            ]);

            // Check payment status
            if ($paymentIntent->status === 'succeeded') {
                // Create payment record
                $payment = $this->recordPayment($enrollment, 'stripe', $paymentIntent->id, [
                    'payment_intent_id' => $paymentIntent->id,
                    'status' => $paymentIntent->status,
                    'amount_received' => $paymentIntent->amount_received,
                ]);

                $payment->markCompleted($paymentIntent->id);

                return [
                    'success' => true,
                    'payment' => $payment,
                    'payment_intent' => $paymentIntent->id,
                ];
            }

            return [
                'success' => false,
                'error' => 'Payment requires additional action or failed.',
                'status' => $paymentIntent->status,
            ];

        } catch (\Stripe\Exception\CardException $e) {
            Log::error('Stripe card error', [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage(),
                'code' => $e->getStripeCode(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => $e->getStripeCode(),
            ];

        } catch (\Exception $e) {
            Log::error('Stripe payment error', [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Payment processing failed. Please try again.',
            ];
        }
    }

    /**
     * Process payment with SSLCommerz (Bangladesh)
     */
    public function processSSLCommerz(Enrollment $enrollment): array
    {
        $postData = [
            'store_id' => config('services.sslcommerz.store_id'),
            'store_passwd' => config('services.sslcommerz.store_password'),
            'total_amount' => $enrollment->amount,
            'currency' => 'BDT',
            'tran_id' => 'TXN-' . Str::random(12),
            'success_url' => route('payment.sslcommerz.success'),
            'fail_url' => route('payment.sslcommerz.fail'),
            'cancel_url' => route('payment.sslcommerz.cancel'),
            'ipn_url' => route('payment.sslcommerz.ipn'),
            'product_name' => $enrollment->course->title,
            'product_category' => 'Online Course',
            'cus_name' => $enrollment->user->name,
            'cus_email' => $enrollment->user->email,
            'cus_phone' => $enrollment->user->phone ?? 'N/A',
            'cus_add1' => 'N/A',
            'cus_city' => 'N/A',
            'cus_country' => 'Bangladesh',
            'shipping_method' => 'NO',
            'num_of_item' => 1,
            'product_profile' => 'non-physical-goods',
            'value_a' => $enrollment->id, // Store enrollment ID for callback
        ];

        $baseUrl = config('services.sslcommerz.sandbox')
            ? 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php'
            : 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';

        try {
            $response = \Illuminate\Support\Facades\Http::asForm()
                ->post($baseUrl, $postData);

            $result = $response->json();

            if ($result['status'] === 'SUCCESS') {
                // Create pending payment record
                $this->recordPayment($enrollment, 'sslcommerz', $postData['tran_id'], [
                    'session_key' => $result['sessionkey'],
                ]);

                return [
                    'success' => true,
                    'gateway_url' => $result['GatewayPageURL'],
                    'session_key' => $result['sessionkey'],
                ];
            }

            return [
                'success' => false,
                'error' => $result['failedreason'] ?? 'Failed to initialize payment',
            ];

        } catch (\Exception $e) {
            Log::error('SSLCommerz error', [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Payment initialization failed.',
            ];
        }
    }

    /**
     * Record payment in database
     */
    protected function recordPayment(
        Enrollment $enrollment,
        string $gateway,
        string $gatewayTransactionId,
        array $gatewayResponse = []
    ): Payment {
        return Payment::create([
            'user_id' => $enrollment->user_id,
            'enrollment_id' => $enrollment->id,
            'transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
            'gateway' => $gateway,
            'gateway_transaction_id' => $gatewayTransactionId,
            'amount' => $enrollment->amount,
            'currency' => $enrollment->currency,
            'status' => 'pending',
            'type' => 'enrollment',
            'gateway_response' => $gatewayResponse,
        ]);
    }

    /**
     * Process refund via Stripe
     */
    public function refundStripe(Payment $payment, float $amount = null): array
    {
        try {
            $refundAmount = $amount ?? $payment->amount;

            $refund = \Stripe\Refund::create([
                'payment_intent' => $payment->gateway_transaction_id,
                'amount' => (int) ($refundAmount * 100),
            ]);

            $payment->processRefund($refundAmount, 'Customer requested refund');

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'amount' => $refundAmount,
            ];

        } catch (\Exception $e) {
            Log::error('Stripe refund error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create Stripe SetupIntent for saving cards
     */
    public function createSetupIntent($user): array
    {
        try {
            $setupIntent = \Stripe\SetupIntent::create([
                'customer' => $this->getOrCreateStripeCustomer($user),
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            return [
                'success' => true,
                'client_secret' => $setupIntent->client_secret,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get or create Stripe customer for user
     */
    protected function getOrCreateStripeCustomer($user): string
    {
        // Check if user already has a Stripe customer ID
        // This would typically be stored in a stripe_customer_id column

        $customer = \Stripe\Customer::create([
            'email' => $user->email,
            'name' => $user->name,
            'phone' => $user->phone,
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);

        return $customer->id;
    }
}
