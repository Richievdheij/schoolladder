<?php
ob_start();
session_start();

if (!empty($_SESSION['user'])) {
    header('Location: /');
    exit;
}

include_once("../../includes/config.php");
$db = db();

$defaultError = "Email of wachtwoord incorrect";

$pageTitle = 'Login | Schoolladder';
$errors = [];

if (isset($_POST['submit'])) {
    $email = htmlentities($_POST['email']);
    $password = $_POST['password'];

    if (!isset($email) || $email === '') {
        $errors[] = "E-mailadres is verplicht";
    }

    if (!isset($password) || $password === '') {
        $errors[] = "Wachtwoord is verplicht";
    }

    if (empty($errors)) {
        $query = "SELECT * FROM `users` WHERE email = :email;";
        $statement = $db->prepare($query);
        $statement->execute([":email" => $email]);

        if (($user = $statement->fetch()) === false) {
            $errors[] = $defaultError;
        }
    }

    if (empty($errors) && !empty($user)) {
        if (!password_verify($password, $user['password'])) {
            $errors[] = $defaultError;
        }
    }

    if (empty($errors) && !empty($user)) {
        $_SESSION['user'] = $user;
        header('Location: /');
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="nl">
<?php require __DIR__ . '/../../includes/header.php'; ?>

<body>
    <?php require __DIR__ . '/../../includes/navbar.php'; ?>
    <main class="page" style="display: flex; flex-direction: column; margin-top: 1rem;">
        <div class="container">
            <div class="logoContainer">
                <h1 style="flex: 1;">Login</h1>
                <img class="logo" alt="Schoolladder Logo" src="/images/schoolladder-logo.png" />
            </div>
            <p>De plek waar je alles kunt zien wat je nodig hebt voor school!</p>
        </div>
        <form action="" method="post" style="display: flex; flex-direction: column;">
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

    <?php require __DIR__ . '/../../includes/bottom-nav.php'; ?>

</body>

<script src="<?= BASE_URL ?>js/navbar.js"></script>
<script src="<?= BASE_URL ?>js/main.js"></script>

</html>