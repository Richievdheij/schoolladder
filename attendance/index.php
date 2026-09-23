<?php
ob_start();
session_start();

$page = "attendance";

if (empty($_SESSION['user'])) {
    header('Location: /portal/login');
    exit;
}

// include_once("../../includes/config.php");
// $db = db();

// $defaultError = "Email of wachtwoord incorrect";

// $pageTitle = 'Login | Schoolladder';
// $errors = [];

// if (isset($_POST['submit'])) {
//     $email = htmlentities($_POST['email']);
//     $password = $_POST['password'];

//     if (!isset($email) || $email === '') {
//         $errors[] = "E-mailadres is verplicht";
//     }

//     if (!isset($password) || $password === '') {
//         $errors[] = "Wachtwoord is verplicht";
//     }

//     if (empty($errors)) {
//         $query = "SELECT * FROM `users` WHERE email = :email;";
//         $statement = $db->prepare($query);
//         $statement->execute([":email" => $email]);

//         if (($user = $statement->fetch()) === false) {
//             $errors[] = $defaultError;
//         }
//     }

//     if (empty($errors) && !empty($user)) {
//         if (!password_verify($password, $user['password'])) {
//             $errors[] = $defaultError;
//         }
//     }

//     if (empty($errors) && !empty($user)) {
//         $_SESSION['user'] = $user;
//         header('Location: /');
//         exit;
//     }
// }

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
            <div class="aanwezigheidsKaart">
                <div>
                    <div>
                        <?php include("../images/icons/user-check.svg"); ?>
                        <div>
                            <h5>Aardrijkskunde</h5>
                            <div>
                                <p>Les</p>
                                <p>|</p>
                                <p>Op tijd</p>
                            </div>
                        </div>
                    </div>
                    <p>12:00-12:45</p>
                </div>
            </div>
            <div class="aanwezigheidsKaart afwezig">
                <div>
                    <div>
                        <?php include("../images/icons/user-check.svg"); ?>
                        <div>
                            <h5>Aardrijkskunde</h5>
                            <div>
                                <p>Huiswerk</p>
                                <p>|</p>
                                <p>Afwezig</p>
                            </div>
                        </div>
                    </div>
                    <p>12:00-12:45</p>
                </div>
            </div>
            <div class="aanwezigheidsKaart te-laat">
                <div>
                    <div>
                        <?php include("../images/icons/user-check.svg"); ?>
                        <div>
                            <h5>Aardrijkskunde</h5>
                            <div>
                                <p>SO</p>
                                <p>|</p>
                                <p>Te laat</p>
                            </div>
                        </div>
                    </div>
                    <p>12:00-12:45</p>
                </div>
            </div>
        </div>

        <div class="container section" style="margin-top: 8px;">
            <h3>Laatste 7 dagen</h3>
            <div class="aanwezigheidsKaart">
                <div>
                    <div>
                        <?php include("../images/icons/user-check.svg"); ?>
                        <div>
                            <h5>Aardrijkskunde</h5>
                            <div>
                                <p>Les</p>
                                <p>|</p>
                                <p>Op tijd</p>
                            </div>
                        </div>
                    </div>
                    <div class="time">
                        <p>12:00-12:45</p>
                        <p>22/09/2026</p>
                    </div>
                </div>
            </div>
            <div class="aanwezigheidsKaart afwezig">
                <div>
                    <div>
                        <?php include("../images/icons/user-check.svg"); ?>
                        <div>
                            <h5>Aardrijkskunde</h5>
                            <div>
                                <p>Huiswerk</p>
                                <p>|</p>
                                <p>Afwezig</p>
                            </div>
                        </div>
                    </div>
                    <div class="time">
                        <p>12:00-12:45</p>
                        <p>22/09/2026</p>
                    </div>
                </div>
            </div>
            <div class="aanwezigheidsKaart te-laat">
                <div>
                    <div>
                        <?php include("../images/icons/user-check.svg"); ?>
                        <div>
                            <h5>Aardrijkskunde</h5>
                            <div>
                                <p>SO</p>
                                <p>|</p>
                                <p>Te laat</p>
                            </div>
                        </div>
                    </div>
                    <div class="time">
                        <p>12:00-12:45</p>
                        <p>22/09/2026</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require __DIR__ . '/../includes/bottom-nav.php'; ?>

</body>

<script src="<?= BASE_URL ?>js/navbar.js"></script>
<script src="<?= BASE_URL ?>js/main.js"></script>

</html>