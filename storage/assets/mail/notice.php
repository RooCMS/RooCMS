<?php
/**
 * Email template: Notice
 * Template for notifications to users
 * 
 * Available variables:
 * $title - title of the notification
 * $message - text of the notification
 * $user_name - name of the user (optional)
 * $site_name - name of the site
 * $site_url - URL of the site
 * $action_url - link for action (optional)
 * $action_text - text of the action button (optional)
 */

// Default values
$title = $title ?? 'Notification';
$message = $message ?? 'You have a new notification.';
$site_name = $site_name ?? 'RooCMS';
$site_url = $site_url ?? '#';
$user_name = $user_name ?? '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
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
            border-bottom: 2px solid #007cba;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #007cba;
            margin: 0;
            font-size: 24px;
        }
        .content {
            margin-bottom: 30px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .message {
            background-color: #f8f9fa;
            padding: 20px;
            border-left: 4px solid #007cba;
            margin: 20px 0;
            border-radius: 4px;
        }
        .action-button {
            text-align: center;
            margin: 30px 0;
        }
        .action-button a {
            display: inline-block;
            padding: 12px 30px;
            background-color: #007cba;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .action-button a:hover {
            background-color: #005a87;
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
            color: #007cba;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?= htmlspecialchars($title) ?></h1>
        </div>
        
        <div class="content">
            <?php if (!empty($user_name)): ?>
                <div class="greeting">
                    Hello, <?= htmlspecialchars($user_name) ?>!
                </div>
            <?php endif; ?>
            
            <div class="message">
                <?= nl2br(htmlspecialchars($message)) ?>
            </div>
            
            <?php if (!empty($action_url) && !empty($action_text)): ?>
                <div class="action-button">
                    <a href="<?= htmlspecialchars($action_url) ?>"><?= htmlspecialchars($action_text) ?></a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="footer">
            <p>
                With respect,<br>
                team <a href="<?= htmlspecialchars($site_url) ?>"><?= htmlspecialchars($site_name) ?></a>
            </p>
            <p style="font-size: 12px; color: #999;">
                This is an automatic message, do not reply to it.
            </p>
        </div>
    </div>
</body>
</html>
