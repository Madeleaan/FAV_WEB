<?php
session_start();
if (isset($_SESSION['login'])) header('Location: /'); ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <title>Title - Přihlášení</title> <!-- TODO branding-->

    <?php include ('template/commonhead.php') ?>
</head>
<body>
<?php include ('template/navigation.php') ?>
<?php include ('template/footer.php') ?>
</body>
</html>
