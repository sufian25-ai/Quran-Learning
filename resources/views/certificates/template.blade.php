<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Completion</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            width: 297mm;
            height: 210mm;
            position: relative;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .certificate-container {
            width: 100%;
            height: 100%;
            padding: 40px 60px;
            position: relative;
            background-image: url('data:image/svg+xml,%3Csvg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"%3E%3Cpath d="M50 0 L60 40 L100 50 L60 60 L50 100 L40 60 L0 50 L40 40 Z" fill="%23ffffff" opacity="0.05"/%3E%3C/svg%3E');
            background-repeat: repeat;
            background-size: 100px;
        }

        .border {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 3px solid #10b981;
            border-radius: 20px;
        }

        .inner-border {
            position: absolute;
            top: 28px;
            left: 28px;
            right: 28px;
            bottom: 28px;
            border: 1px solid #059669;
            border-radius: 15px;
            background: white;
            box-shadow: inset 0 0 50px rgba(16, 185, 129, 0.1);
        }

        .content {
            position: relative;
            z-index: 10;
            text-align: center;
            padding-top: 50px;
        }

        .logo {
            font-size: 48px;
            font-weight: bold;
            color: #10b981;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .logo-subtitle {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 30px;
            letter-spacing: 3px;
        }

        .certificate-title {
            font-size: 56px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 20px;
            color: #6b7280;
            margin-bottom: 40px;
        }

        .presented-to {
            font-size: 18px;
            color: #6b7280;
            margin-bottom: 15px;
            font-style: italic;
        }

        .student-name {
            font-size: 42px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 30px;
            border-bottom: 2px solid #10b981;
            display: inline-block;
            padding: 0 30px 10px;
        }

        .description {
            font-size: 16px;
            color: #4b5563;
            margin-bottom: 15px;
            line-height: 1.8;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .course-name {
            font-size: 24px;
            font-weight: bold;
            color: #10b981;
            margin-bottom: 40px;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            max-width: 800px;
            margin: 50px auto 0;
            padding: 0 50px;
        }

        .signature-block {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-top: 2px solid #1f2937;
            margin-bottom: 5px;
            padding-top: 40px;
        }

        .signature-name {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
        }

        .signature-title {
            font-size: 12px;
            color: #6b7280;
            font-style: italic;
        }

        .certificate-meta {
            position: absolute;
            bottom: 50px;
            left: 60px;
            right: 60px;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #9ca3af;
        }

        .qr-code {
            position: absolute;
            bottom: 45px;
            right: 60px;
            width: 80px;
            height: 80px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #9ca3af;
        }

        .date {
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <div class="border"></div>
        <div class="inner-border"></div>

        <div class="content">
            <div class="logo">📖 QuranLearn</div>
            <div class="logo-subtitle">ISLAMIC LEARNING ACADEMY</div>

            <div class="certificate-title">Certificate</div>
            <div class="subtitle">of Achievement</div>

            <div class="presented-to">This is to certify that</div>
            <div class="student-name">{{ $certificate->student_name }}</div>

            <div class="description">
                has successfully completed the course
            </div>

            <div class="course-name">{{ $certificate->course_title }}</div>

            <div class="description">
                with a completion rate of <strong>{{ number_format($certificate->completion_percentage, 0) }}%</strong>
                @if($certificate->grade)
                    and achieved a grade of <strong>{{ number_format($certificate->grade, 0) }}%</strong>
                @endif
            </div>

            <div class="footer">
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $certificate->instructor_name ?? 'Chief Instructor' }}</div>
                    <div class="signature-title">Course Instructor</div>
                </div>

                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $certificate->issued_by ?? 'Admin Director' }}</div>
                    <div class="signature-title">QuranLearn Director</div>
                    <div class="date">{{ $certificate->course_completed_at->format('F d, Y') }}</div>
                </div>
            </div>
        </div>

        <div class="certificate-meta">
            <div>
                <div><strong>Certificate No:</strong> {{ $certificate->certificate_number }}</div>
                <div><strong>Verification Code:</strong> {{ $certificate->verification_code }}</div>
            </div>
            <div style="text-align: right;">
                <div><strong>Issue Date:</strong> {{ $certificate->created_at->format('F d, Y') }}</div>
                <div><strong>Verify at:</strong> quranlearn.com/verify</div>
            </div>
        </div>

        <div class="qr-code">
            QR Code
        </div>
    </div>
</body>

</html>