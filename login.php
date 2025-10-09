<?php
session_start();
if (isset($_SESSION['login'])) header('Location: /') ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <title>Title - Přihlášení</title> <!-- TODO branding-->

    <style>
        .nav-link.active {
            background-color: var(--bs-secondary) !important
        }
    </style>

    <?php include ('template/commonhead.php') ?>
</head>

<body>
<?php include ('template/navigation.php') ?>

<div class="container-fluid p-5 my-sm-auto">
    <div class="row">
        <div class="col-lg-3 col-sm-1"></div>
        <div class="card col-lg-6 col-sm-10">
            <ul class="nav nav-pills row border-bottom py-2">
                <li class="nav-item text-center col-md-6">
                    <button class="nav-link active w-100" data-bs-toggle="pill" data-bs-target="#login">Přihlášení</button>
                </li>
                <li class="nav-item text-center col-md-6">
                    <button class="nav-link w-100" data-bs-toggle="pill" data-bs-target="#register">Registrace</button>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active py-2" id="login">
                    <form class="row g-3" id="login-form">
                        <div class="col-md-6">
                            <label for="login-login" class="form-label">Uživatelské jméno</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="login-login" name="login" required>
                                <div class="invalid-feedback" id="login-login-feedback">Uživatelské jméno neexistuje!</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="login-pass" class="form-label">Heslo</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="login-pass" name="password" required>
                                <div class="invalid-feedback" id="login-pass-feedback">Špatně zadané heslo!</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary col-12 col-md-4" type="submit">Přihlásit</button>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="register"><p>Register</p></div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-1"></div>
    </div>
</div>

<?php include ('template/footer.php') ?>
</body>

<script>
    $("#login-form").submit(function(e)  {
        e.preventDefault()
        $("input").each((_, el) => el.classList.remove('is-invalid'))

        let formData = $(this).serializeArray()
        let postData = {}
        $.each(formData, (k, v) => postData[v.name] = v.value)
        $.ajax({
            url: '/api/login',
            type: 'POST',
            data: JSON.stringify(postData),
            processData: false,
            contentType: false,
            success: () => {
                let ref = document.referrer

                if (ref && ref !== '') location.replace(ref)
                else location.replace('/')
            },
            error: (data) => {
                let msg = JSON.parse(data.responseText)
                if (msg.error === 'BAD_LOGIN') $("#login-login").addClass('is-invalid')
                else if (msg.error === 'BAD_PASS') $("#login-pass").addClass('is-invalid')
            }
        })
    })
</script>
</html>
