<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MystiDraw</title>
    <style>
        /* Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #334155;
            background-color: #f9fafb;
        }

        /* Container */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            text-align: center;
            padding: 40px 30px;
        }

        .logo {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .logo-icon {
            display: inline-block;
            margin-right: 8px;
            font-size: 28px;
        }

        .tagline {
            font-size: 16px;
            color: #fbbf24;
            font-weight: 600;
            opacity: 0.95;
        }

        /* Content Area */
        .content {
            padding: 40px 30px;
        }

        /* Footer */
        .footer {
            background-color: #0f172a;
            color: #94a3b8;
            padding: 30px;
            text-align: center;
        }

        .footer-content {
            margin-bottom: 20px;
        }

        .footer-logo {
            color: #fbbf24;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .footer-text {
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .contact-info {
            font-size: 13px;
            color: #64748b;
            border-top: 1px solid #1e293b;
            padding-top: 20px;
        }

        .social-links {
            margin: 15px 0;
        }

        .social-links a {
            display: inline-block;
            margin: 0 8px;
            color: #fbbf24;
            text-decoration: none;
            font-size: 14px;
        }

        .social-links a:hover {
            color: #f59e0b;
        }

        /* Utilities */
        .text-center { text-align: center; }
        .text-navy { color: #1e3a8a; }
        .text-gold { color: #fbbf24; }
        .font-bold { font-weight: 700; }
        .mb-4 { margin-bottom: 16px; }
        .mb-6 { margin-bottom: 24px; }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .header {
                padding: 30px 20px;
            }
            
            .content {
                padding: 30px 20px;
            }
            
            .footer {
                padding: 25px 20px;
            }
            
            .logo {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <span class="logo-icon">🎲</span>
                MystiDraw
            </div>
            <div class="tagline">Wo jedes Los ein Gewinn ist!</div>
        </div>

        <!-- Content -->
        <div class="content">
            @yield('content')
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-content">
                <div class="footer-logo">🎲 MystiDraw</div>
                <div class="footer-text">
                    Deine Mystery-Box Plattform mit 100% Gewinngarantie.<br>
                    Sammle, überrasche dich und lass dir deine Gewinne kostenlos zusenden!
                </div>
                
                <div class="social-links">
                    <a href="#" style="text-decoration: none;">📧 Support</a>
                    <a href="#" style="text-decoration: none;">📱 Social Media</a>
                    <a href="#" style="text-decoration: none;">🌐 Website</a>
                </div>
            </div>
            
            <div class="contact-info">
                <div>© {{ date('Y') }} MystiDraw. Alle Rechte vorbehalten.</div>
                <div style="margin-top: 8px;">
                    Kostenloser Versand aus Deutschland • 1-3 Werktage Lieferzeit
                </div>
            </div>
        </div>
    </div>
</body>
</html>
