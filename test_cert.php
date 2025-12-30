<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $enrollment = \App\Models\Enrollment::with(['user', 'course', 'batch.teacher'])->first();

    if (!$enrollment) {
        echo "No enrollment found\n";
        exit;
    }

    echo "Enrollment ID: {$enrollment->id}\n";
    echo "User: {$enrollment->user->name}\n";
    echo "Course: {$enrollment->course->title}\n";

    $service = new \App\Services\CertificateService();

    echo "Generating certificate...\n";
    $certificate = $service->generateCertificate($enrollment);

    echo "SUCCESS!\n";
    echo "Certificate Number: {$certificate->certificate_number}\n";
    echo "Verification Code: {$certificate->verification_code}\n";
    echo "PDF Path: {$certificate->pdf_path}\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString();
}
