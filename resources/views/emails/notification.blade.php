<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $appName }} - Notification</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-bottom: 15px;
            background-color: white;
            display: inline-block;
            padding: 10px;
        }
        .app-name {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        .content {
            padding: 30px 20px;
        }
        .message {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }
        .message-text {
            font-size: 16px;
            margin: 0;
            color: #555;
        }
        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
            transition: all 0.3s ease;
        }
        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .footer-text {
            color: #6c757d;
            font-size: 14px;
            margin: 0;
        }
        .divider {
            height: 1px;
            background-color: #e9ecef;
            margin: 20px 0;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 4px;
            }
            .header {
                padding: 20px 15px;
            }
            .content {
                padding: 20px 15px;
            }
        }
        [dir="rtl"] .message {
            border-left: none;
            border-right: 4px solid #667eea;
            border-radius: 4px 0 0 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if($appLogo)
                <img src="{{ asset($appLogo) }}" alt="{{ $appName }}" class="logo">
            @endif
            <h1 class="app-name">{{ $appName }}</h1>
        </div>
        
        <div class="content">
            <div class="message">
                <p class="message-text">{{ $notificationMessage }}</p>
            </div>
            
            @if($actionRoute)
                <div style="text-align: center;">
                    <a href="{{ url($actionRoute . ($actionParams ? '/' . $actionParams : '')) }}" class="action-button">
                        {{ __('common.view_details') }}
                    </a>
                </div>
            @endif
            
            <div class="divider"></div>
            
            <p style="color: #6c757d; font-size: 14px; margin: 0;">
                {{ __('common.automated_notification') }} {{ $appName }}.
                {{ __('common.contact_support') }}
            </p>
        </div>
        
        <div class="footer">
            <p class="footer-text">
                {{ __('©') }} {{ date('Y') }} {{ $appName }}. {{ __('common.all_rights_reserved') }}
            </p>
        </div>
    </div>
</body>
</html> 