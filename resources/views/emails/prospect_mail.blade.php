<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <div style="white-space: pre-line;">
        {{ $messageBody }}
    </div>
    
    <br><br>
    <div style="border-top: 1px solid #eee; padding-top: 10px; font-size: 12px; color: #777;">
        <p>Ce message a été envoyé par <strong>{{ auth()->user()->name ?? 'Notre équipe' }}</strong> via le CRM.</p>
    </div>

</body>
</html>
