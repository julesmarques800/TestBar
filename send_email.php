<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Charger PHPMailer

// Vérifier que les données sont bien envoyées en JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo "Aucune donnée reçue.";
    exit;
}

$mail = new PHPMailer(true);

try {
    // Configuration du serveur SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Serveur SMTP Gmail
    $mail->SMTPAuth = true;
    $mail->Username = 'jules.marques@2027.icam.fr'; // Ton adresse Gmail
    $mail->Password = 'gevr thjz kcnt ktlt'; // Mot de passe Gmail ou App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Récupération des données du client
    $client_name = htmlspecialchars($data["name"]);
    $client_email = htmlspecialchars($data["email"]);
    $client_phone = htmlspecialchars($data["phone"]);
    $client_message = nl2br(htmlspecialchars($data["message"]));

    // 📌 Expéditeur : TOI (obligatoire)
    $mail->setFrom('ton-email@gmail.com', 'Site Web - Demande de Devis');

    // 📌 Destinataire : TOI (où tu reçois le message)
    $mail->addAddress('ton-email@gmail.com', 'Ton Nom');

    // 📌 Reply-To : Email du client (quand tu réponds, ça va directement vers lui)
    $mail->addReplyTo($client_email, $client_name);

    // 📌 Contenu de l'email (structuré avec la commande du client)
    $email_body = "<h2>Nouvelle Demande de Devis</h2>";
    $email_body .= "<p><strong>Nom :</strong> $client_name</p>";
    $email_body .= "<p><strong>Email :</strong> $client_email</p>";
    $email_body .= "<p><strong>Téléphone :</strong> $client_phone</p>";
    $email_body .= "<p><strong>Message :</strong> $client_message</p>";

    // 📌 Ajout du résumé de la commande
    if (!empty($data["cart"])) {
        $email_body .= "<h3>Résumé de la commande :</h3><ul>";
        foreach ($data["cart"] as $item) {
            $colors = implode(", ", array_map("htmlspecialchars", $item["colors"]));
            $email_body .= "<li><strong>Modèle :</strong> " . htmlspecialchars($item["model"]) . 
                        " | <strong>Couleurs :</strong> " . $colors .
                        " | <strong>Quantité :</strong> " . htmlspecialchars($item["quantity"]) . "</li>";
        }
        $email_body .= "</ul>";
    }

    // 📌 Configuration de l'email
    $mail->isHTML(true);
    $mail->Subject = "Nouvelle Demande de Devis de " . $client_name;
    $mail->Body = $email_body;

    // ✅ Envoi de l'email
    $mail->send();
    echo "Votre demande de devis a été envoyée avec succès !";

} catch (Exception $e) {
    echo "Erreur lors de l'envoi du message : " . $mail->ErrorInfo;
}
?>
