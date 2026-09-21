<?php
$pageTitle = 'Schoolladder';

?>
<!DOCTYPE html>
<html lang="nl">
<?php require __DIR__ . '/../../includes/header.php'; ?>

<body>
    <main style="display: flex; flex-direction: column; gap: 1em;">
        <div class="container">
            <h1>Login</h1>
            <p>De plek waar je alles kunt zien wat je nodig hebt voor school!</p>
        </div>
        <form action="" method="post" class="container">
            <div class="error">
                email of wachtwoord verkeerd!
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" name="email">
            </div>

            <div>
                <label for="password">Wachtwoord</label>
                <input type="password" name="password">
            </div>
            <div>
                <button type="submit" name="submit">Login</button>
            </div>
            <div>
                <p>Nog geen inlog? <a href="/portal/register">Registreer nu!</a></p>
            </div>
        </form>
    </main>

    <?php require __DIR__ . '/../../includes/footer.php'; ?>

</body>

</html>