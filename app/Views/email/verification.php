<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Email Verification') ?></title>
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
            background: #c0392b;
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
            <h2>Verify Your Email Address / अपना ईमेल सत्यापित करें</h2>

            <p>Thank you for registering with Hind Bihar. Please click the button below to verify your email address:</p>
            <p class="hindi">Hind Bihar में पंजीकरण के लिए धन्यवाद। कृपया अपना ईमेल सत्यापित करने के लिए नीचे दिए गए बटन पर क्लिक करें:</p>

            <p style="text-align: center;">
                <a href="<?= esc($verification_link ?? '#') ?>" class="button">Verify Email / ईमेल सत्यापित करें</a>
            </p>

            <p>Or copy and paste this link into your browser:</p>
            <p style="word-break: break-all; font-size: 0.9rem; color: #666;"><?= esc($verification_link ?? '#') ?></p>

            <p>This link will not expire. If you did not create an account, you can safely ignore this email.</p>
            <p class="hindi">यदि आपने खाता नहीं बनाया है, तो कृपया इस ईमेल को अनदेखा करें।</p>
        </div>
        <div class="footer">
            &copy; <?= date('Y') ?> Hind Bihar. All rights reserved.
        </div>
    </div>
</body>
</html>
