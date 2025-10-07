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

<div class="d-flex justify-content-center align-items-center mt-5">
    <div class="card w-50">
        <ul class="nav nav-tabs">
            <li class="nav-item text-center w-50">
                <button class="nav-link active w-100" data-bs-toggle="pill" data-bs-target="#login">Login</button>
            </li>
            <li class="nav-item text-center w-50">
                <button class="nav-link w-100" data-bs-toggle="pill" data-bs-target="#register">Register</button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="login"><p>Login</p></div>
            <div class="tab-pane fade" id="register"><p>Register</p></div>
        </div>
    </div>
</div>

<?php include ('template/footer.php') ?>
</body>
</html>
