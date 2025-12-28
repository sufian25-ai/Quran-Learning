<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    auth: Object,
    settings: {
        type: Object,
        default: () => ({})
    }
});

const activeTab = ref('general');

const tabs = [
    { id: 'general', label: 'General', icon: '⚙️' },
    { id: 'payment', label: 'Payment', icon: '💳' },
    { id: 'email', label: 'Email', icon: '📧' },
    { id: 'zoom', label: 'Zoom', icon: '🎥' },
];

const generalForm = useForm({
    site_name: props.settings?.site_name || 'Quran Learning Platform',
    site_email: props.settings?.site_email || 'info@quranlearning.com',
    site_phone: props.settings?.site_phone || '+1 (555) 123-4567',
    timezone: props.settings?.timezone || 'UTC',
    currency: props.settings?.currency || 'USD',
    support_email: props.settings?.support_email || 'support@quranlearning.com',
});

const paymentForm = useForm({
    stripe_enabled: props.settings?.stripe_enabled ?? true,
    stripe_public_key: props.settings?.stripe_public_key || '',
    stripe_secret_key: props.settings?.stripe_secret_key || '',
    sslcommerz_enabled: props.settings?.sslcommerz_enabled ?? false,
    sslcommerz_store_id: props.settings?.sslcommerz_store_id || '',
    sslcommerz_store_password: props.settings?.sslcommerz_store_password || '',
});

const emailForm = useForm({
    mail_driver: props.settings?.mail_driver || 'smtp',
    mail_host: props.settings?.mail_host || '',
    mail_port: props.settings?.mail_port || '587',
    mail_username: props.settings?.mail_username || '',
    mail_password: props.settings?.mail_password || '',
    mail_from_address: props.settings?.mail_from_address || '',
    mail_from_name: props.settings?.mail_from_name || 'Quran Learning',
});

const zoomForm = useForm({
    zoom_enabled: props.settings?.zoom_enabled ?? true,
    zoom_account_id: props.settings?.zoom_account_id || '',
    zoom_client_id: props.settings?.zoom_client_id || '',
    zoom_client_secret: props.settings?.zoom_client_secret || '',
});

const saveGeneral = () => {
    generalForm.post('/admin/settings/general', {
        preserveScroll: true,
    });
};

const savePayment = () => {
    paymentForm.post('/admin/settings/payment', {
        preserveScroll: true,
    });
};

const saveEmail = () => {
    emailForm.post('/admin/settings/email', {
        preserveScroll: true,
    });
};

const saveZoom = () => {
    zoomForm.post('/admin/settings/zoom', {
        preserveScroll: true,
    });
};

const timezones = [
    'UTC', 'America/New_York', 'America/Los_Angeles', 'Europe/London',
    'Europe/Paris', 'Asia/Dubai', 'Asia/Dhaka', 'Asia/Kolkata',
    'Asia/Singapore', 'Australia/Sydney'
];
</script>

<template>
    <Head title="Settings | Admin" />

    <AdminLayout>
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
        </div>

        <div class="flex gap-6">
            <!-- Tabs Sidebar -->
            <div class="w-64 flex-shrink-0">
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <nav class="space-y-1">
                        <button
                            v-for="tab in tabs"
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            :class="[
                                'w-full flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-colors',
                                activeTab === tab.id
                                    ? 'bg-primary-50 text-primary-700'
                                    : 'text-gray-600 hover:bg-gray-50'
                            ]"
                        >
                            <span class="text-lg mr-3">{{ tab.icon }}</span>
                            {{ tab.label }}
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1">
                <!-- General Settings -->
                <div v-if="activeTab === 'general'" class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">General Settings</h2>
                    <form @submit.prevent="saveGeneral" class="space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label for="site_name" class="block text-sm font-medium text-gray-700 mb-2">Site Name</label>
                                <input
                                    id="site_name"
                                    v-model="generalForm.site_name"
                                    type="text"
                                    class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                />
                            </div>
                            <div>
                                <label for="site_email" class="block text-sm font-medium text-gray-700 mb-2">Site Email</label>
                                <input
                                    id="site_email"
                                    v-model="generalForm.site_email"
                                    type="email"
                                    class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                />
                            </div>
                            <div>
                                <label for="site_phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input
                                    id="site_phone"
                                    v-model="generalForm.site_phone"
                                    type="text"
                                    class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                />
                            </div>
                            <div>
                                <label for="support_email" class="block text-sm font-medium text-gray-700 mb-2">Support Email</label>
                                <input
                                    id="support_email"
                                    v-model="generalForm.support_email"
                                    type="email"
                                    class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                />
                            </div>
                            <div>
                                <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                                <select
                                    id="timezone"
                                    v-model="generalForm.timezone"
                                    class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                >
                                    <option v-for="tz in timezones" :key="tz" :value="tz">{{ tz }}</option>
                                </select>
                            </div>
                            <div>
                                <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                                <select
                                    id="currency"
                                    v-model="generalForm.currency"
                                    class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                >
                                    <option value="USD">USD ($)</option>
                                    <option value="EUR">EUR (€)</option>
                                    <option value="GBP">GBP (£)</option>
                                    <option value="BDT">BDT (৳)</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="generalForm.processing"
                                class="px-6 py-2 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg disabled:opacity-50"
                            >
                                {{ generalForm.processing ? 'Saving...' : 'Save Changes' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Payment Settings -->
                <div v-if="activeTab === 'payment'" class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Payment Settings</h2>
                    <form @submit.prevent="savePayment" class="space-y-8">
                        <!-- Stripe -->
                        <div class="border-b border-gray-200 pb-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-3">💳</span>
                                    <h3 class="font-medium text-gray-900">Stripe</h3>
                                </div>
                                <label class="flex items-center cursor-pointer">
                                    <input
                                        v-model="paymentForm.stripe_enabled"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-primary-500 focus:ring-primary-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-600">Enabled</span>
                                </label>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Public Key</label>
                                    <input
                                        v-model="paymentForm.stripe_public_key"
                                        type="text"
                                        placeholder="pk_..."
                                        class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Secret Key</label>
                                    <input
                                        v-model="paymentForm.stripe_secret_key"
                                        type="password"
                                        placeholder="sk_..."
                                        class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- SSLCommerz -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-3">🏦</span>
                                    <h3 class="font-medium text-gray-900">SSLCommerz</h3>
                                </div>
                                <label class="flex items-center cursor-pointer">
                                    <input
                                        v-model="paymentForm.sslcommerz_enabled"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-primary-500 focus:ring-primary-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-600">Enabled</span>
                                </label>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Store ID</label>
                                    <input
                                        v-model="paymentForm.sslcommerz_store_id"
                                        type="text"
                                        class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Store Password</label>
                                    <input
                                        v-model="paymentForm.sslcommerz_store_password"
                                        type="password"
                                        class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="paymentForm.processing"
                                class="px-6 py-2 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg disabled:opacity-50"
                            >
                                {{ paymentForm.processing ? 'Saving...' : 'Save Changes' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Email Settings -->
                <div v-if="activeTab === 'email'" class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Email Settings</h2>
                    <form @submit.prevent="saveEmail" class="space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Mail Driver</label>
                                <select
                                    v-model="emailForm.mail_driver"
                                    class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                >
                                    <option value="smtp">SMTP</option>
                                    <option value="sendgrid">SendGrid</option>
                                    <option value="mailgun">Mailgun</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Mail Host</label>
                                <input
                                    v-model="emailForm.mail_host"
                                    type="text"
                                    placeholder="smtp.example.com"
                                    class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Port</label>
                                <input
                                    v-model="emailForm.mail_port"
                                    type="text"
                                    class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                                <input
                                    v-model="emailForm.mail_username"
                                    type="text"
                                    class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                <input
                                    v-model="emailForm.mail_password"
                                    type="password"
                                    class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">From Address</label>
                                <input
                                    v-model="emailForm.mail_from_address"
                                    type="email"
                                    class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                />
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="emailForm.processing"
                                class="px-6 py-2 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg disabled:opacity-50"
                            >
                                {{ emailForm.processing ? 'Saving...' : 'Save Changes' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Zoom Settings -->
                <div v-if="activeTab === 'zoom'" class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Zoom Integration</h2>
                    <form @submit.prevent="saveZoom" class="space-y-6">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-gray-600">Enable automatic Zoom meeting creation for classes</p>
                            <label class="flex items-center cursor-pointer">
                                <input
                                    v-model="zoomForm.zoom_enabled"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-primary-500 focus:ring-primary-500"
                                />
                                <span class="ml-2 text-sm text-gray-600">Enabled</span>
                            </label>
                        </div>
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Account ID</label>
                                <input
                                    v-model="zoomForm.zoom_account_id"
                                    type="text"
                                    class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Client ID</label>
                                <input
                                    v-model="zoomForm.zoom_client_id"
                                    type="text"
                                    class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Client Secret</label>
                                <input
                                    v-model="zoomForm.zoom_client_secret"
                                    type="password"
                                    class="w-full rounded-lg border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                />
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="zoomForm.processing"
                                class="px-6 py-2 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg disabled:opacity-50"
                            >
                                {{ zoomForm.processing ? 'Saving...' : 'Save Changes' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
