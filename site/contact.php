<?php
$status = isset($_GET['status']) ? preg_replace('/[^a-z]/', '', $_GET['status']) : '';
$flash = null;

if ($status === 'sent') {
    $flash = ['type' => 'success', 'text' => 'Bedankt! Je bericht is verstuurd. We reageren snel.'];
} elseif ($status === 'validation') {
    $flash = ['type' => 'error', 'text' => 'Controleer je gegevens: naam, e-mail en bericht zijn verplicht.'];
} elseif ($status === 'failed') {
    $flash = ['type' => 'error', 'text' => 'Versturen lukte niet. Probeer het opnieuw of mail direct naar nick.esselman@gmail.com.'];
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jazz Design | Contact</title>
    <link rel="icon" href="images/logoico.ico" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="site-header">
        <div class="shell navbar">
            <a class="brand" href="index.php">
                <img src="images/logo.webp" alt="Jazz Design logo">
                <span>Jazz Design</span>
            </a>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="about.php">Over ons</a>
                <a class="btn btn-ghost" href="contact.php">Contact</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="page-hero">
            <div class="shell">
                <span class="tag">Contact</span>
                <h1>Vertel ons over je idee, team of evenement.</h1>
                <p class="lead">Laat weten wat je wilt laten maken en hoeveel stuks je nodig hebt. We reageren snel met een voorstel of vragen.</p>
            </div>
        </section>

        <section class="shell contact-layout">
            <div class="contact-card">
                <h3>Vertel ons je plannen</h3>
                <p class="subtle">Vul je gegevens in. We sturen zo snel mogelijk een reactie terug met een inschatting of een paar gerichte vragen.</p>
                <?php if ($flash): ?>
                    <div class="flash <?= $flash['type']; ?>"><?= $flash['text']; ?></div>
                <?php endif; ?>
                <form action="send_email.php" method="post">
                    <label for="name">Naam</label>
                    <input type="text" id="name" name="name" autocomplete="name" required>

                    <label for="email">E-mailadres</label>
                    <input type="email" id="email" name="email" autocomplete="email" required>

                    <label for="project_type">Wat wil je laten maken?</label>
                    <select id="project_type" name="project_type">
                        <option value="Teamwear">Teamwear / clubkleding</option>
                        <option value="Bedrijfskleding">Bedrijfskleding</option>
                        <option value="Dans & sport">Dans / sport</option>
                        <option value="Merch drop">Merch drop / limited</option>
                        <option value="Anders">Anders / weet ik nog niet</option>
                    </select>

                    <label for="message">Bericht</label>
                    <textarea id="message" name="message" placeholder="Aantal stuks, type kleding en wens voor kleuren of deadline" required></textarea>

                    <div class="hidden-field" aria-hidden="true">
                        <label for="website">Laat dit leeg</label>
                        <input type="hidden" id="website" name="website">
                    </div>

                    <button type="submit" class="btn btn-primary">Verstuur bericht</button>
                </form>
                <p class="subtle">Je bericht komt direct bij Jessie binnen. We slaan niets op voor andere doeleinden.</p>
            </div>

            <div class="contact-card">
                <h3>Liever direct contact?</h3>
                <p>Mail of bel ons gerust. Deel wat je voor ogen hebt, dan plannen we snel een call of sturen we een sample visual.</p>
                <p><strong>E-mail</strong><br>
                    <a href="mailto:nick.esselman@gmail.com">nick.esselman@gmail.com</a></p>
                <p><strong>Instagram</strong><br>
                    <a href="https://instagram.com" target="_blank" rel="noreferrer">@jazzdesign</a></p>
                <p><strong>Werkwijze</strong><br>
                    Snelle proef, transparante prijzen en persoonlijke begeleiding voor elke oplage.</p>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="shell">
            <div>(c) 2024 Jazz Design. Persoonlijk contact en snelle productie.</div>
            <div>Mail ons: <a href="mailto:nick.esselman@gmail.com">nick.esselman@gmail.com</a></div>
        </div>
    </footer>
</body>
</html>
