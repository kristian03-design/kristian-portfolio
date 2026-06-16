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
            padding: 30px;
            text-align: center;
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
        }
        .logo-text {
            color: #ffffff;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin: 0;
        }
        .content {
            padding: 40px;
            color: #334155;
            line-height: 1.6;
        }
        .greeting {
            font-size: 16px;
            font-weight: 600;
            color: #0F172A;
            margin-bottom: 16px;
        }
        .message-body {
            font-size: 15px;
            margin-bottom: 30px;
            white-space: pre-wrap;
        }
        .signature {
            font-size: 15px;
            font-weight: 600;
            color: #0F172A;
            margin-top: 20px;
        }
        .quote-container {
            margin-top: 40px;
            padding: 20px;
            background-color: #F8FAFC;
            border-left: 4px solid #CBD5E1;
            border-radius: 0 12px 12px 0;
        }
        .quote-header {
            font-size: 13px;
            font-weight: 700;
            color: #64748B;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .quote-body {
            font-size: 14px;
            color: #475569;
            font-style: italic;
            white-space: pre-wrap;
        }
        .footer {
            padding: 32px;
            text-align: center;
            font-size: 12px;
            color: #94A3B8;
            border-top: 1px solid #F1F5F9;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <p class="logo-text">Kristian Hernandez &mdash; Portfolio</p>
            </div>
            <div class="content">
                <p class="greeting">Hello {{ $originalMessage->name }},</p>
                
                <div class="message-body">{!! nl2br(e($replyBody)) !!}</div>
                
                <p class="signature">Best regards,<br>Kristian Hernandez</p>
                
                <div class="quote-container">
                    <div class="quote-header">Original Message Sent on {{ $originalMessage->created_at->format('M d, Y h:i A') }}</div>
                    <div class="quote-body">"{{ $originalMessage->message }}"</div>
                </div>
            </div>
            <div class="footer">
                &copy; {{ date('Y') }} Kristian Hernandez. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
