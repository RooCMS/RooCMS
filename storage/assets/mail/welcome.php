<?php
/**
 * Email template: Welcome
 * Template for welcome message for new users
 * 
 * Available variables:
 * $user_name - name of the user
 * $user_email - email of the user
 * $site_name - name of the site
 * $site_url - URL of the site
 * $login_url - link for login
 * $support_email - email of the support (optional)
 * $activation_url - link for activation (optional)
 * $password - temporary password (optional)
 */

// Default values
$user_name = $user_name ?? 'User';
$site_name = $site_name ?? 'RooCMS';
$site_url = $site_url ?? '#';
$login_url = $login_url ?? $site_url;
$support_email = $support_email ?? 'support@' . parse_url($site_url, PHP_URL_HOST);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to <?= htmlspecialchars($site_name) ?>!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #28a745;
            margin: 0;
            font-size: 28px;
        }
        .header .subtitle {
            color: #666;
            font-size: 16px;
            margin-top: 10px;
        }
        .welcome-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #28a745;
            font-weight: bold;
        }
        .info-box {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .credentials {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .credentials strong {
            color: #856404;
        }
        .action-buttons {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }
        .btn-primary {
            background-color: #28a745;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #218838;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: #ffffff;
        }
        .btn-secondary:hover {
            background-color: #545b62;
        }
        .features {
            margin: 30px 0;
        }
        .features h3 {
            color: #28a745;
            margin-bottom: 15px;
        }
        .features ul {
            list-style: none;
            padding: 0;
        }
        .features li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
        }
        .features li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 20px;
            margin-top: 30px;
            font-size: 14px;
            color: #666;
        }
        .footer a {
            color: #28a745;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="welcome-icon">🎉</div>
            <h1>Welcome!</h1>
            <div class="subtitle">You have successfully registered on <?= htmlspecialchars($site_name) ?></div>
        </div>
        
        <div class="content">
            <div class="greeting">
                Hello, <?= htmlspecialchars($user_name) ?>!
            </div>
            
            <div class="info-box">
                <p>Thank you for registering on our site! We are glad to welcome you to our community.</p>
                
                <?php if (!empty($user_email)): ?>
                <p><strong>Your email:</strong> <?= htmlspecialchars($user_email) ?></p>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($password)): ?>
            <div class="credentials">
                <p><strong>Your login data:</strong></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user_email) ?></p>
                <p><strong>Temporary password:</strong> <?= htmlspecialchars($password) ?></p>
                <p><em>We recommend changing the password after the first login to the system.</em></p>
            </div>
            <?php endif; ?>
            
            <div class="action-buttons">
                <?php if (!empty($activation_url)): ?>
                    <a href="<?= htmlspecialchars($activation_url) ?>" class="btn btn-primary">Activate account</a>
                <?php endif; ?>
                <a href="<?= htmlspecialchars($login_url) ?>" class="btn btn-secondary">Login to the system</a>
            </div>
            
            <div class="features">
                <h3>What awaits you:</h3>
                <ul>
                    <li>Full access to all site functions</li>
                    <li>Personal user profile</li>
                    <li>Ability to leave comments</li>
                    <li>Notifications about new materials</li>
                    <li>Technical support</li>
                </ul>
            </div>
            
            <div class="info-box">
                <p><strong>Need help?</strong></p>
                <p>If you have any questions, please contact us at: 
                   <a href="mailto:<?= htmlspecialchars($support_email) ?>"><?= htmlspecialchars($support_email) ?></a>
                </p>
            </div>
        </div>
        
        <div class="footer">
            <p>
                With respect,<br>
                команда <a href="<?= htmlspecialchars($site_url) ?>"><?= htmlspecialchars($site_name) ?></a>
            </p>
            <p style="font-size: 12px; color: #999;">
                This is an automatic message, do not reply to it.
            </p>
        </div>
    </div>
</body>
</html>
