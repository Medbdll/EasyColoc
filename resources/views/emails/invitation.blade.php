<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You're Invited!</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-weight: bold;
            font-size: 24px;
        }
        h1 {
            color: #1a202c;
            font-size: 28px;
            margin-bottom: 10px;
        }
        .invitation-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin: 30px 0;
            text-align: center;
        }
        .colocation-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .personal-message {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
            font-style: italic;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .button:hover {
            transform: translateY(-2px);
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 14px;
        }
        .details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .detail-label {
            font-weight: 600;
            color: #4a5568;
        }
        .detail-value {
            color: #2d3748;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">EC</div>
            <h1>You're Invited to Join! 🎉</h1>
            <p>You've been invited to join a colocation on EasyColoc</p>
        </div>

        <div class="invitation-card">
            <div class="colocation-name">{{ $colocation->name }}</div>
            <p>Click the button below to accept this invitation and start managing expenses together!</p>
            <a href="{{ $acceptUrl }}" class="button">Accept Invitation</a>
        </div>

        @if($personalMessage)
            <div class="personal-message">
                <strong>Personal message from the inviter:</strong><br>
                "{{ $personalMessage }}"
            </div>
        @endif

        <div class="details">
            <h3>Colocation Details:</h3>
            <div class="detail-item">
                <span class="detail-label">Name:</span>
                <span class="detail-value">{{ $colocation->name }}</span>
            </div>
            @if($colocation->description)
                <div class="detail-item">
                    <span class="detail-label">Description:</span>
                    <span class="detail-value">{{ $colocation->description }}</span>
                </div>
            @endif
            <div class="detail-item">
                <span class="detail-label">Current Members:</span>
                <span class="detail-value">{{ $colocation->users->count() }}</span>
            </div>
        </div>

        <div class="footer">
            <p>This invitation was sent to {{ $invitation->email }}</p>
            <p>If you didn't expect this invitation, you can safely ignore this email.</p>
            <p>&copy; {{ date('Y') }} EasyColoc. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
