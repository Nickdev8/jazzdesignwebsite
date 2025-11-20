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

// SMTP settings from environment (.env loaded via docker-compose env_file)
$smtpHost = getenv('SMTP_HOST') ?: '';
$smtpPort = getenv('SMTP_PORT') ?: '';
$smtpUser = getenv('SMTP_USER') ?: '';
$smtpPassword = getenv('SMTP_PASSWORD') ?: '';
$smtpSecure = strtolower(getenv('SMTP_SECURE') ?: ''); // '', 'ssl', 'tls'

/**
 * Minimal SMTP sender so we don't rely on PHP mail() being set up.
 */
function send_via_smtp(
    string $host,
    int $port,
    string $user,
    string $pass,
    string $secure,
    string $fromEmail,
    string $fromName,
    string $toEmail,
    string $subject,
    string $body,
    string $replyName,
    string $replyEmail
): bool {
    $prefix = $secure === 'ssl' ? 'ssl://' : 'tcp://';
    $remote = $prefix . $host . ':' . $port;
    $conn = @stream_socket_client($remote, $errno, $errstr, 10);
    if (!$conn) {
        return false;
    }

    $read = function () use ($conn) {
        $data = '';
        while ($line = fgets($conn, 515)) {
            $data .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return $data;
    };
    $write = function (string $cmd) use ($conn, $read) {
        fwrite($conn, $cmd . "\r\n");
        return $read();
    };

    $read(); // server banner
    $write('EHLO jazzdesign.local');

    if ($secure === 'tls') {
        $write('STARTTLS');
        stream_socket_enable_crypto($conn, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        $write('EHLO jazzdesign.local');
    }

    $write('AUTH LOGIN');
    $write(base64_encode($user));
    $write(base64_encode($pass));

    $write('MAIL FROM:<' . $fromEmail . '>');
    $write('RCPT TO:<' . $toEmail . '>');
    $write('DATA');

    $headers = [
        'From: ' . $fromName . ' <' . $fromEmail . '>',
        'To: ' . $toEmail,
        'Reply-To: ' . $replyName . ' <' . $replyEmail . '>',
        'Subject: ' . $subject,
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
    ];

    $payload = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.";
    $write($payload);
    $write('QUIT');
    fclose($conn);
    return true;
}

// Use SMTP if credentials are present, otherwise fall back to mail()
$sent = false;
if ($smtpHost && $smtpPort && $smtpUser && $smtpPassword) {
    $fromEmail = $smtpUser;
    $fromName = 'Jazz Design Contact';
    $sent = send_via_smtp(
        $smtpHost,
        (int)$smtpPort,
        $smtpUser,
        $smtpPassword,
        $smtpSecure,
        $fromEmail,
        $fromName,
        $recipient,
        $subject,
        $body,
        $name,
        $email
    );
} else {
    $headers = [
        'From: Jazz Design Contact <no-reply@jazzdesign.local>',
        'Reply-To: ' . $name . ' <' . $email . '>',
        'Content-Type: text/plain; charset=UTF-8',
    ];
    $sent = mail($recipient, $subject, $body, implode("\r\n", $headers));
}

if ($sent) {
    header('Location: contact.php?status=sent');
} else {
    header('Location: contact.php?status=failed');
}

exit;
