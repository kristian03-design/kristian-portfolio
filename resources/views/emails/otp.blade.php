<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #F8FAFC;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #F8FAFC;
            padding-bottom: 40px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            margin-top: 40px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            border: 1px solid #F1F5F9;
        }
        .header {
            padding: 40px;
            text-align: center;
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
        }
        .logo-text {
            color: #ffffff;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin: 0;
        }
        .content {
            padding: 48px 40px;
            text-align: center;
            color: #475569;
        }
        .title {
            font-size: 20px;
            font-weight: 800;
            color: #0F172A;
            margin-bottom: 16px;
        }
        .description {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 32px;
        }
        .otp-container {
            margin: 32px 0;
            padding: 24px;
            background-color: #F8FAFC;
            border-radius: 16px;
            border: 1px dashed #CBD5E1;
            display: inline-block;
        }
        .otp-code {
            font-size: 40px;
            font-weight: 800;
            color: #4F46E5;
            letter-spacing: 12px;
            padding-left: 12px;
            margin: 0;
        }
        .footer {
            padding: 32px;
            text-align: center;
            font-size: 12px;
            color: #94A3B8;
            border-top: 1px solid #F1F5F9;
        }
        .warning {
            font-size: 13px;
            color: #94A3B8;
            margin-top: 32px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <p class="logo-text">ADMIN SECURITY</p>
            </div>
            <div class="content">
                <h2 class="title">Verification Code</h2>
                <p class="description">You are receiving this because a login attempt was made to your admin dashboard. Use the code below to complete your authentication:</p>
                
                <div class="otp-container">
                    <p class="otp-code">{{ $otp }}</p>
                </div>

                <p class="warning">This code will expire in 10 minutes. If you did not request this, please secure your account immediately.</p>
            </div>
            <div class="footer">
                &copy; {{ date('Y') }} Kristian Hernandez Portfolio. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
