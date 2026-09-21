<?php
$pageTitle = 'Register | Schoolladder';

$errors = null;

if (isset($_POST['submit'])) {
    $name = htmlentities($_POST['name']);
    $email = htmlentities($_POST['email']);
    $password = htmlentities($_POST['password']);
    $password_confirm = htmlentities($_POST['password-confirm']);

    if (!isset($name) || $name === '') {
        $errors[] = "Naam is verplicht";
    }

    if (!isset($email) || $email === '') {
        $errors[] = "E-mailadres is verplicht";
    }

    if (!isset($password) || $password === '') {
        $errors[] = "Wachtwoord is verplicht";
    }

    if (!isset($password_confirm) || $password_confirm === '') {
        $errors[] = "Wachtwoord herhaling is verplicht";
    }

    if ($password !== $password_confirm) {
        $errors[] = "Wachtwoorden moeten hetzelfde zijn";
    }
}

?>
<!DOCTYPE html>
<html lang="nl">
<?php require __DIR__ . '/../../includes/header.php'; ?>

<body>
    <main style="display: flex; flex-direction: column; gap: 1em;">
        <div class="container">
            <h1>Register</h1>
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
                <div class="<?= (isset($name) && $name === '') ? 'error-input' : ''; ?>">
                    <label for="name">Name</label>
                    <input type="text" name="name" placeholder="John Doe" value="<?= (isset($name) && $name !== '') ? $name : '' ?>">
                </div>
                <div class="<?= (isset($email) && $email === '') ? 'error-input' : ''; ?>">
                    <label for="email">Email</label>
                    <input type="email" name="email" placeholder="john.doe@example.com" value="<?= (isset($email) && $email !== '') ? $email : '' ?>">
                </div>
            </div>

            <div class="container section">
                <div class="<?= (isset($password) && $password === '') ? 'error-input' : ''; ?>">
                    <label for="password">Wachtwoord</label>
                    <input type="password" name="password" placeholder="*******" value="<?= (isset($password) && $password !== '') ? $password : '' ?>">
                </div>
                <div class="<?= (isset($password_confirm) && $password_confirm === '') ? 'error-input' : ''; ?>">
                    <label for="password-confirm">Herhaal wachtwoord</label>
                    <input type="password" name="password-confirm" placeholder="*******" value="<?= (isset($password_confirm) && $password_confirm !== '') ? $password_confirm : '' ?>">
                </div>
            </div>
            <div class="container">
                <button type="submit" name="submit">Registreer</button>
                <div>
                    <p>Al een inlog? <a href="/portal/login">Ga naar login!</a></p>
                </div>
            </div>
        </form>
    </main>

    <?php require __DIR__ . '/../../includes/footer.php'; ?>

</body>

</html>