<?php
ob_start();
session_start();

$page = "attendance";

if (empty($_SESSION['user'])) {
    header('Location: /portal/login');
    exit;
}

require_once __DIR__ . '/../includes/data/user.php';
require_once __DIR__ . '/../includes/data/attendance.php';

/** @var array $attendance */

?>
<!DOCTYPE html>
<html lang="nl">
<?php require __DIR__ . '/../includes/header.php'; ?>

<body>
    <?php require __DIR__ . '/../includes/navbar.php'; ?>
    <main class="page" style="display: flex; flex-direction: column; margin-top: 1rem;">
        <div class="container">
            <div class="logoContainer">
                <h1 style="flex: 1;">Aanwezigheid</h1>
            </div>
            <p>Hier kan je de aanwezigheid voor de afgelopen lessen terug kijken</p>
        </div>

        <div class="container section" style="margin-top: 8px;">
            <h3>Vandaag</h3>

            <?php if ($attendance['today'] === []): ?>
                <?= emptyState(
                    'user-check',
                    'Nog geen aanwezigheid vandaag.',
                    'Vanaf je eersteles ziet het systeem waar je bent.'
                ) ?>
            <?php else: ?>
                <?php foreach ($attendance['today'] as $card): ?>
                    <div class="aanwezigheidsKaart<?= $card['class'] !== '' ? ' ' . e($card['class']) : '' ?>">
                        <div>
                            <div>
                                <?= icon('user-check') ?>
                                <div>
                                    <h5><?= e($card['subject']) ?></h5>
                                    <div>
                                        <p><?= e($card['kind']) ?></p>
                                        <p>|</p>
                                        <p><?= e($card['label']) ?></p>
                                    </div>
                                </div>
                            </div>
                            <p><?= e($card['time']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="container section" style="margin-top: 8px;">
            <h3>Laatste 7 dagen</h3>

            <?php if ($attendance['last7Days'] === []): ?>
                <?= emptyState(
                    'user-check',
                    'Nog geen registraties in de laatste 7 dagen.',
                    'Zodra een docent je aanwezigheid heeft bijgehouden, staat het hier.'
                ) ?>
            <?php else: ?>
                <?php foreach ($attendance['last7Days'] as $card): ?>
                    <div class="aanwezigheidsKaart<?= $card['class'] !== '' ? ' ' . e($card['class']) : '' ?>">
                        <div>
                            <div>
                                <?= icon('user-check') ?>
                                <div>
                                    <h5><?= e($card['subject']) ?></h5>
                                    <div>
                                        <p><?= e($card['kind']) ?></p>
                                        <p>|</p>
                                        <p><?= e($card['label']) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="time">
                                <p><?= e($card['time']) ?></p>
                                <p><?= e($card['when']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php require __DIR__ . '/../includes/bottom-nav.php'; ?>

</body>

<script src="<?= BASE_URL ?>js/navbar.js"></script>
<script src="<?= BASE_URL ?>js/main.js"></script>

</html>