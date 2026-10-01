<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your OTP Code - Wandr Jewelry</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background: #faf8f3;
            margin: 0;
            padding: 0;
            width: 100% !important;
        }
        .container {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            text-align: center;
            padding: 48px 32px 40px 32px;
            background-color: #fff;
            max-width: 460px;
            margin: 48px auto;
            border-radius: 18px;
            box-shadow: 0 12px 32px 0 rgba(197,160,33, 0.09), 0 1.5px 6px 0 rgba(197,160,33, 0.04);
            transition: box-shadow 0.2s;
        }
        .logo {
            color: #C5A021;
            font-size: 35px;
            font-weight: 900;
            margin-bottom: 22px;
            letter-spacing: 2.5px;
            font-family: 'Georgia', serif;
            text-shadow: 0px 3px 14px #fffbc98a;
        }
        .message {
            color: #2d2d2d;
            font-size: 18px;
            margin-bottom: 32px;
            font-weight: 400;
            line-height: 1.7;
        }
        .code {
            letter-spacing: 16px;
            font-size: 50px;
            font-weight: 900;
            color: #C5A021;
            background: #f9f7f0;
            margin: 34px 0 32px 0;
            border: 2.7px solid #eae2c6;
            border-radius: 13px;
            padding: 24px 22px;
            display: inline-block;
            font-family: monospace, 'Helvetica Neue', Helvetica, Arial, sans-serif;
            box-shadow: 0 4px 16px rgba(197,160,33, 0.07);
            transition: box-shadow 0.18s;
            user-select: all;
        }
        .expiration-note {
            color: #806e38;
            font-size: 16px;
            background: #faefa7;
            margin: 24px auto 18px auto;
            padding: 13px 18px;
            display: inline-block;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.7px;
            box-shadow: 0 1px 4px rgba(197,160,33, 0.13);
        }
        .instructions {
            color: #888;
            font-size: 15px;
            margin-top: 16px;
            margin-bottom: 26px;
            line-height: 1.7;
        }
        .footer {
            color: #bcbcbc;
            font-size: 12px;
            margin-top: 48px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }
        @media only screen and (max-width: 600px) {
            .container {
                padding: 22px 8vw 26px 8vw;
                margin: 14vw auto;
                max-width: 95vw;
                border-radius: 10px;
            }
            .logo {
                font-size: 7.8vw;
            }
            .message {
                font-size: 4vw;
            }
            .code {
                font-size: 11vw;
                letter-spacing: 7vw;
                padding: 10vw 0vw;
            }
            .expiration-note {
                font-size: 3.7vw;
                padding: 3vw 4vw;
                border-radius: 2vw;
            }
            .instructions {
                font-size: 4vw;
                margin-top: 6vw;
                margin-bottom: 7vw;
            }
            .footer {
                font-size: 3vw;
                margin-top: 10vw;
            }
        }

        /* Enhancements for dark mode in compatible mail clients  */
        @media (prefers-color-scheme: dark) {
            body {
                background: #191715 !important;
            }
            .container {
                background: #23201B !important;
                color: #fff;
                box-shadow: none;
            }
            .logo,
            .code,
            .expiration-note {
                color: #F2D25C !important;
                background: #373119 !important;
            }
            .expiration-note {
                background: #392f11 !important;
            }
            .footer {
                color: #eee !important;
            }
        }
    </style>
</head>
<body style="background: #faf8f3; margin: 0; padding: 0; width: 100% !important;">
    <div class="container">
        <div class="logo" style="font-size: 35px;">WANDR JEWELRY</div>
        <div class="message">
            Hello,<br>
            <span style="color:#C5A021; font-weight: bold;">Here is your One-Time Passcode (OTP)</span>
            <br>
            <span style="font-size: 15px; color: #616060;">Use it to verify your identity and keep your Wandr account safe.</span>
        </div>
        <div class="code" aria-label="Verification code" style="letter-spacing: 16px; font-size: 50px; font-weight:900;">{{ $otp }}</div>
        <div class="expiration-note">
            <span style="vertical-align: middle; margin-right:6px;">&#9201;</span>
            This code expires in <strong>5 minutes</strong> for your security.
        </div>
        <div class="instructions">
            <strong>Didn't request this?</strong> You can safely ignore this email.<br>
            <span style="color: #c67332;">Never share your code. Our team will never ask for it.</span>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Wandr Jewelry. All rights reserved.<br>
            <span style="color:#c5a021; font-weight:bold;">Beautiful. Secure. Memorable.</span>
        </div>
    </div>
</body>
</html>