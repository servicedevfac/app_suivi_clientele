<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur le CRM - Définissez votre mot de passe</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #334155;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); overflow: hidden; border: 1px solid #f1f5f9;">
                    <!-- En-tête -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); padding: 32px 40px; text-align: center;">
                            <h1 style="color: #ffffff; font-size: 24px; font-weight: 700; margin: 0; letter-spacing: -0.5px;">CRM Commercial</h1>
                            <p style="color: #e0e7ff; font-size: 14px; margin: 8px 0 0 0;">Activation de votre compte collaborateur</p>
                        </td>
                    </tr>

                    <!-- Contenu -->
                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="color: #0f172a; font-size: 20px; font-weight: 600; margin: 0 0 16px 0;">Bonjour {{ $user->prenom ?: $user->nom }},</h2>
                            
                            <p style="font-size: 15px; line-height: 1.6; color: #475569; margin: 0 0 24px 0;">
                                Un compte a été créé pour vous sur l'application <strong>CRM Commercial</strong> avec l'adresse email : <span style="color: #0f172a; font-weight: 600;">{{ $user->email }}</span>.
                            </p>

                            <p style="font-size: 15px; line-height: 1.6; color: #475569; margin: 0 0 24px 0;">
                                Pour des raisons de sécurité, vous devez définir votre mot de passe initial avant de pouvoir vous connecter.
                            </p>

                            <!-- Bouton d'action -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 32px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $resetUrl }}" style="display: inline-block; background-color: #4f46e5; color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none; padding: 14px 32px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.25);">
                                            Définir mon mot de passe
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Règles de sécurité -->
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin: 28px 0;">
                                <h3 style="color: #0f172a; font-size: 14px; font-weight: 600; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 0.5px;">🔒 Exigences de sécurité pour votre mot de passe</h3>
                                <ul style="margin: 0; padding-left: 20px; font-size: 13px; color: #64748b; line-height: 1.8;">
                                    <li>Au moins <strong>8 caractères</strong></li>
                                    <li>Au moins une <strong>lettre majuscule</strong> (A-Z)</li>
                                    <li>Au moins une <strong>lettre minuscule</strong> (a-z)</li>
                                    <li>Au moins un <strong>chiffre</strong> (0-9)</li>
                                    <li>Au moins un <strong>caractère spécial</strong> (!@#$%^&* etc.)</li>
                                </ul>
                            </div>

                            <p style="font-size: 13px; color: #94a3b8; line-height: 1.6; margin: 24px 0 0 0;">
                                Si vous ne parvenez pas à cliquer sur le bouton, copiez et collez le lien ci-dessous dans votre navigateur web :<br>
                                <a href="{{ $resetUrl }}" style="color: #4f46e5; word-break: break-all;">{{ $resetUrl }}</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Pied de page -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #f1f5f9; padding: 24px 40px; text-align: center;">
                            <p style="font-size: 12px; color: #94a3b8; margin: 0;">
                                Ce message est un email automatique de sécurité envoyé par l'application CRM Commercial.<br>
                                Ne répondez pas à cet e-mail.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
