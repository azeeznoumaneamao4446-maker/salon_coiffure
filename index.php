<?php
declare(strict_types=1);

header_remove('X-Powered-By');
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header("Content-Security-Policy: default-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'; object-src 'none'");

require_once __DIR__ . '/db.php';

$prestations = [];
$databaseUnavailable = false;

try {
    $statement = database()->query(
        'SELECT libelle, prix, duree FROM prestation ORDER BY libelle'
    );
    $prestations = $statement->fetchAll();
} catch (PDOException | RuntimeException $exception) {
    error_log('Erreur de connexion ou de lecture de la base de données : ' . $exception->getMessage());
    http_response_code(500);
    $databaseUnavailable = true;
}

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Salon de coiffure - Prestations</title>
</head>
<body>
    <main>
        <h1>Nos prestations</h1>
        <?php if ($databaseUnavailable): ?>
            <p>Les prestations sont temporairement indisponibles. Veuillez réessayer plus tard.</p>
        <?php elseif ($prestations === []): ?>
            <p>Aucune prestation n'est disponible pour le moment.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($prestations as $prestation): ?>
                    <li>
                        <strong><?= escape((string) $prestation['libelle']) ?></strong>
                        — <?= escape((string) $prestation['prix']) ?> €
                        (<?= escape((string) $prestation['duree']) ?> min)
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </main>
</body>
</html>
