<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vérifie si les données sont envoyées en JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    die("Aucune donnée reçue.");
}

// Récupération des données du formulaire
$client_name = htmlspecialchars($data["name"]);
$client_email = htmlspecialchars($data["email"]);
$client_phone = htmlspecialchars($data["phone"]);
$client_message = nl2br(htmlspecialchars($data["message"]));

// 📌 Destinataire (Ton email perso)
$to = "jules.marques@2027.icam.fr"; // Remplace par ton adresse email

// 📌 Sujet de l'email
$subject = "Nouvelle Demande de Devis de $client_name";

// 📌 Construction du message
$message = "
<h2>Nouvelle Demande de Devis</h2>
<p><strong>Nom :</strong> $client_name</p>
<p><strong>Email :</strong> $client_email</p>
<p><strong>Téléphone :</strong> $client_phone</p>
<p><strong>Message :</strong> $client_message</p>
<h3>Résumé de la commande :</h3>
<ul>";

// 📌 Ajout du résumé de la commande
if (!empty($data["cart"])) {
    foreach ($data["cart"] as $item) {
        $colors = implode(", ", array_map("htmlspecialchars", $item["colors"]));
        $message .= "<li><strong>Modèle :</strong> " . htmlspecialchars($item["model"]) .
                    " | <strong>Couleurs :</strong> " . $colors .
                    " | <strong>Quantité :</strong> " . htmlspecialchars($item["quantity"]) . "</li>";
    }
}
$message .= "</ul>";

// 📌 En-têtes pour un email HTML
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "From: $client_email\r\n";
$headers .= "Reply-To: $client_email\r\n";

// 📌 Envoi de l'email
if (mail($to, $subject, $message, $headers)) {
    echo "Votre demande de devis a été envoyée avec succès !";
} else {
    echo "Erreur lors de l'envoi du message.";
}
?>
