<?php
ob_start();
session_start();

if (!empty($_SESSION['user'])) {
    header('Location: /');
    exit;
}

include_once("../../includes/config.php");
$db = db();

$pageTitle = 'Register | Schoolladder';

$errors = null;

if (isset($_POST['submit'])) {
    $name = htmlentities($_POST['name']);
    $email = htmlentities($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password-confirm'];

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

    if (empty($errors)) {
        $password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO `users` SET name = :name, email = :email, password = :password;";
        $statement = $db->prepare($query);

        if ($statement->execute([":name" => $name, ":email" => $email, ":password" => $password])) {
            header('Location: /portal/login');
            exit;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="nl">
<?php require __DIR__ . '/../../includes/header.php'; ?>

<body>
    <?php require __DIR__ . '/../../includes/navbar.php'; ?>
    <main class="page" style="margin-top: 1rem;">
        <div class="container">
            <div class="logoContainer">
                <h1 style="flex: 1;">Register</h1>
                <img class="logo" alt="Schoolladder Logo" src="/images/schoolladder-logo.png" />
            </div>
            <p>De plek waar je alles kunt zien wat je nodig hebt voor school!</p>
        </div>
        <form action="" method="post">
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

    <?php require __DIR__ . '/../../includes/bottom-nav.php'; ?>

</body>

<script src="<?= BASE_URL ?>js/navbar.js"></script>
<script src="<?= BASE_URL ?>js/main.js"></script>

</html>