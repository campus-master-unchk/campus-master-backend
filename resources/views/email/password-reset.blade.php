<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #4a90e2;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 5px 5px;
            border: 1px solid #ddd;
        }

        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .button {
            display: inline-block;
            background-color: #4a90e2;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }

        .token-info {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            word-break: break-all;
            font-family: monospace;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Réinitialisation de mot de passe</h1>
    </div>

    <div class="content">
        <p>Bonjour <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>,</p>

        <p>Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.</p>

        <p>Pour réinitialiser votre mot de passe, cliquez sur le bouton ci-dessous :</p>

        <p style="text-align: center;">
            <a href="{{ $link }}" class="button" target="_blank">
                Réinitialiser mon mot de passe
            </a>
        </p>

        <p>Si le bouton ne fonctionne pas, vous pouvez copier-coller le lien suivant dans votre navigateur :</p>

        <div class="token-info">
            {{ $link }}
        </div>

        <div class="warning">
            <p>⚠️ <strong>Important :</strong></p>
            <ul>
                <li>Ce lien est valable pendant <strong>{{ $expirationMinutes }} minutes</strong></li>
                <li>Ne partagez jamais ce lien avec qui que ce soit</li>
                <li>Si vous n'avez pas demandé de réinitialisation, ignorez simplement cet email</li>
                <li>Le lien expirera à : <strong>{{ now()->addMinutes($expirationMinutes)->format('d/m/Y H:i') }}</strong></li>
            </ul>
        </div>

        <p>Si vous rencontrez des problèmes, n'hésitez pas à nous contacter.</p>

        <p>Cordialement,<br>
            <strong>L'équipe {{ config('app.name') }}</strong>
        </p>
    </div>

    <div class="footer">
        <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
        <p>© {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
    </div>
</body>

</html>