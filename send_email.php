<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Configuration Brevo
    $recipientEmail = 'optiref@gmail.com'; // Où recevoir les messages
    
    // ✅ IMPORTANT : Utilisez l'adresse de CONNEXION Brevo (pas l'expéditeur)
    $brevoLoginEmail = getenv('BREVO_LOGIN_EMAIL'); // Login Brevo (à définir dans l'environnement)
    
    // ✅ La clé SMTP "Mot de passe principal" (cliquez sur l'œil pour la voir)
    $brevoPassword   = getenv('BREVO_SMTP_KEY'); // Clé SMTP à définir dans l'environnement

    if (empty($brevoLoginEmail) || empty($brevoPassword)) {
        error_log('Brevo credentials are not configured.');
        header('Location: index.html?status=error');
        exit;
    }
    
    // L'adresse qui apparaîtra comme expéditeur (doit être validée)
    $senderEmail     = 'formulaire@formateur-ia.eu';
    $senderName      = 'Formulaire Contact IA Formations';

    // Récupération données formulaire
    $prenom = isset($_POST['prenom']) ? htmlspecialchars(trim($_POST['prenom'])) : '';
    $nom = isset($_POST['nom']) ? htmlspecialchars(trim($_POST['nom'])) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $telephone = isset($_POST['telephone']) ? htmlspecialchars(trim($_POST['telephone'])) : '';
    $objet = isset($_POST['objet']) ? htmlspecialchars(trim($_POST['objet'])) : '';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

    $fullName = trim($prenom . ' ' . $nom);

    // Validation
    if (empty($prenom) || empty($nom) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($objet) || empty($message)) {
        header('Location: index.html?status=error');
        exit;
    }

    // Labels pour l'objet
    $objetLabels = [
        'formation-entreprise' => 'Formation en entreprise',
        'formation-centre' => 'Formation pour centre de formation',
        'devis' => 'Demande de devis',
        'information' => 'Demande d\'information',
        'autre' => 'Autre'
    ];
    $objetTexte = isset($objetLabels[$objet]) ? $objetLabels[$objet] : $objet;

    $mail = new PHPMailer(true);

    try {
        // Configuration SMTP Brevo
        $mail->SMTPDebug = 0; // Mettre à 2 pour débugger
        $mail->isSMTP();
        $mail->Host       = 'smtp-relay.brevo.com';
        $mail->SMTPAuth   = true;
        
        // ✅ CRUCIAL : Username = adresse de connexion Brevo
        $mail->Username   = $brevoLoginEmail;
        $mail->Password   = $brevoPassword;
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->Timeout    = 30;

        // Destinataires
        // ✅ From = adresse validée dans Brevo (différente du login)
        $mail->setFrom($senderEmail, $senderName);
        $mail->addAddress($recipientEmail);
        $mail->addReplyTo($email, $fullName);

        // Contenu
        $mail->isHTML(true);
        $mail->Subject = 'IA Formations Bordeaux - ' . $objetTexte;
        
        $mail->Body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; }
                .header { background: linear-gradient(90deg, #4F46E5, #06B6D4); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: white; padding: 30px; border-radius: 0 0 8px 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .field { margin-bottom: 15px; }
                .field-label { font-weight: bold; color: #4F46E5; }
                .field-value { margin-top: 5px; padding: 10px; background: #f5f7ff; border-left: 3px solid #4F46E5; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>📧 Nouveau message - IA Formations Bordeaux</h2>
                </div>
                <div class='content'>
                    <div class='field'>
                        <div class='field-label'>👤 Contact :</div>
                        <div class='field-value'>{$fullName}</div>
                    </div>
                    <div class='field'>
                        <div class='field-label'>📧 Email :</div>
                        <div class='field-value'><a href='mailto:{$email}'>{$email}</a></div>
                    </div>
                    " . (!empty($telephone) ? "
                    <div class='field'>
                        <div class='field-label'>📞 Téléphone :</div>
                        <div class='field-value'>{$telephone}</div>
                    </div>
                    " : "") . "
                    <div class='field'>
                        <div class='field-label'>📋 Objet :</div>
                        <div class='field-value'>{$objetTexte}</div>
                    </div>
                    <div class='field'>
                        <div class='field-label'>💬 Message :</div>
                        <div class='field-value'>" . nl2br($message) . "</div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ";

        $mail->AltBody = "Nouveau message - IA Formations Bordeaux\n\n";
        $mail->AltBody .= "De: {$fullName}\n";
        $mail->AltBody .= "Email: {$email}\n";
        if (!empty($telephone)) {
            $mail->AltBody .= "Téléphone: {$telephone}\n";
        }
        $mail->AltBody .= "Objet: {$objetTexte}\n\n";
        $mail->AltBody .= "Message:\n{$message}";

        $mail->CharSet = 'UTF-8';

        $mail->send();
        header('Location: index.html?status=success');
        exit;

    } catch (Exception $e) {
        error_log("Erreur PHPMailer: {$mail->ErrorInfo}");
        header('Location: index.html?status=error');
        exit;
    }

} else {
    header('Location: index.html');
    exit;
}
?>

