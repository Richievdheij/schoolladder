<?php
$pageTitle = 'Login | Schoolladder';
$errors = [];

if (isset($_POST['submit'])) {
    $email = htmlentities($_POST['email']);
    $password = htmlentities($_POST['password']);

    if (!isset($email) || $email === '') {
        $errors[] = "E-mailadres is verplicht";
    }

    if (!isset($password) || $password === '') {
        $errors[] = "Wachtwoord is verplicht";
    }
}

?>
<!DOCTYPE html>
<html lang="nl">
<?php require __DIR__ . '/../../includes/header.php'; ?>

<body>
    <main style="display: flex; flex-direction: column; gap: 1em;">
        <div class="container">
            <img class="logo" alt="Schoolladder Logo" src="/images/schoolladder-logo.png" />
            <h1>Login</h1>
            <p>De plek waar je alles kunt zien wat je nodig hebt voor school!</p>
        </div>
        <form action="" method="post" style="display: flex; flex-direction: column; gap: 1em;">
            <?php if (!empty($errors)): ?>
                <div class="error">
                    <?php foreach ($errors as $error): ?>
                        <p><?= $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="container section">
                <div class="<?= (isset($email) && $email === '') ? 'error-input' : ''; ?>">
                    <label for="email">Email</label>
                    <input type="email" name="email" placeholder="john.doe@example.com" value="<?= (isset($email) && $email !== '') ? $email : '' ?>">
                </div>
                <div class="<?= (isset($password) && $password === '') ? 'error-input' : ''; ?>">
                    <label for="password">Wachtwoord</label>
                    <input type="password" name="password" placeholder="*******" value="<?= (isset($password) && $password !== '') ? $password : '' ?>">
                </div>
            </div>
            <div class="container">
                <button type="submit" name="submit">Login</button>
                <div>
                    <p>Nog geen inlog? <a href="/portal/register">Registreer nu!</a></p>
                </div>
            </div>
        </form>
    </main>

    <?php require __DIR__ . '/../../includes/footer.php'; ?>

</body>

</html>