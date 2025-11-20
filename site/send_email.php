<?php
declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.php?status=validation');
    exit;
}

$name = trim(strip_tags($_POST['name'] ?? ''));
$email = trim($_POST['email'] ?? '');
$projectType = trim(strip_tags($_POST['project_type'] ?? 'Algemeen'));
$message = trim(strip_tags($_POST['message'] ?? ''));
$honeypot = trim($_POST['website'] ?? '');

if ($honeypot !== '') {
    header('Location: contact.php?status=sent');
    exit;
}

$errors = [];
if ($name === '') {
    $errors[] = 'name';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'email';
}

if (!empty($errors)) {
    header('Location: contact.php?status=validation');
    exit;
}

$recipient = getenv('CONTACT_RECIPIENT') ?: 'nick.esselman@gmail.com';
$subject = 'Jazz Design | Nieuw bericht (' . ($projectType ?: 'Algemeen') . ')';
$body = "Naam: {$name}\nE-mail: {$email}\nProjecttype: {$projectType}\n\nBericht:\n{$message}\n\nVerzonden via het Jazz Design contactformulier.";

$headers = [
    'From: Jazz Design Contact <no-reply@jazzdesign.local>',
    'Reply-To: ' . $name . ' <' . $email . '>',
    'Content-Type: text/plain; charset=UTF-8',
];

$sent = mail($recipient, $subject, $body, implode("\r\n", $headers));

if ($sent) {
    header('Location: contact.php?status=sent');
} else {
    header('Location: contact.php?status=failed');
}

exit;
