<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vos identifiants de connexion</title>
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
            background-color: #0A54CD;
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
        .credentials {
            background-color: #fff;
            border: 2px dashed #0A54CD;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .credentials-item {
            margin-bottom: 10px;
        }
        .credentials-label {
            font-weight: bold;
            color: #0A54CD;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .button {
            display: inline-block;
            background-color: #0A54CD;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Bienvenue sur {{ config('app.name') }}</h1>
    </div>
    
    <div class="content">
        <p>Bonjour <strong>{{ $first_name }} {{ $last_name }}</strong>,</p>
        
        <p>Votre compte a été créé avec succès. Voici vos identifiants de connexion :</p>
        
        <div class="credentials">
            <div class="credentials-item">
                <span class="credentials-label">Email :</span>
                <span>{{ $email }}</span>
            </div>
            
            <div class="credentials-item">
                <span class="credentials-label">Mot de passe temporaire :</span>
                <span><strong>{{ $password }}</strong></span>
            </div>
        </div>
        
        <div class="warning">
            <p> <strong>Important :</strong> Pour des raisons de sécurité, nous vous recommandons de :</p>
            <ol>
                <li>Modifier votre mot de passe dès votre première connexion</li>
                <li>Ne jamais partager vos identifiants</li>
                <li>Utiliser un mot de passe unique et robuste</li>
            </ol>
        </div>
        
        <p>Pour vous connecter, rendez-vous sur notre plateforme :</p>
        
        <p style="text-align: center;">
            <a href="{{ $link }}" class="button">
                Se connecter maintenant
            </a>
        </p>
        
        <p>Si vous rencontrez des problèmes pour vous connecter, n'hésitez pas à nous contacter.</p>
        
        <p>Cordialement,<br>
        <strong>L'équipe {{ config('app.name') }}</strong></p>
    </div>
    
    <div class="footer">
        <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
        <p>© {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
    </div>
</body>
</html>