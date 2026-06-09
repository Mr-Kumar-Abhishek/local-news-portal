<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Password Reset') ?></title>
    <style>
        body {
            font-family: 'Noto Sans', 'Noto Sans Devanagari', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: #2c3e50;
            color: white;
            padding: 24px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 1.5rem;
        }
        .body {
            padding: 24px;
            color: #333;
            line-height: 1.6;
        }
        .button {
            display: inline-block;
            background: #c0392b;
            color: white;
            text-decoration: none;
            padding: 12px 32px;
            border-radius: 4px;
            font-weight: 600;
            margin: 16px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 16px 24px;
            font-size: 0.85rem;
            color: #666;
            text-align: center;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 4px;
            padding: 12px;
            margin: 16px 0;
            font-size: 0.9rem;
        }
        .hindi {
            font-family: 'Noto Sans Devanagari', Arial, sans-serif;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Hind Bihar</h1>
        </div>
        <div class="body">
            <h2>Password Reset Request / पासवर्ड रीसेट अनुरोध</h2>

            <p>You have requested to reset your password. Click the button below to set a new password:</p>
            <p class="hindi">आपने अपना पासवर्ड रीसेट करने का अनुरोध किया है। नया पासवर्ड सेट करने के लिए नीचे दिए गए बटन पर क्लिक करें:</p>

            <p style="text-align: center;">
                <a href="<?= esc($reset_link ?? '#') ?>" class="button">Reset Password / पासवर्ड रीसेट करें</a>
            </p>

            <p>Or copy and paste this link into your browser:</p>
            <p style="word-break: break-all; font-size: 0.9rem; color: #666;"><?= esc($reset_link ?? '#') ?></p>

            <div class="warning">
                <strong>Note:</strong> This link will expire in 1 hour. If you did not request a password reset, please ignore this email.
                <br><span class="hindi">नोट: यह लिंक 1 घंटे में समाप्त हो जाएगा। यदि आपने पासवर्ड रीसेट का अनुरोध नहीं किया है, तो कृपया इस ईमेल को अनदेखा करें।</span>
            </div>
        </div>
        <div class="footer">
            &copy; <?= date('Y') ?> Hind Bihar. All rights reserved.
        </div>
    </div>
</body>
</html>
