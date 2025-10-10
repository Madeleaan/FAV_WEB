<nav class="navbar navbar-expand-lg bg-body-tertiary px-3 border-bottom">
    <a class="navbar-brand" href="/">Navbar</a> <!-- TODO branding -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapsible">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapsible">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <?php
            $links = ["Informace"=>"/", "Články"=>"/clanky"];
            foreach ($links as $label => $url): ?>
            <li class="nav-item">
                <a class="nav-link <?php if (str_ends_with($_SERVER['REQUEST_URI'], $url)) echo 'active'; ?>"
                   href="<?= $url; ?>"><?=$label?></a>
            </li>
            <?php endforeach ?>

        </ul>
        <?php
        if (array_key_exists('login', $_SESSION) && isset($_SESSION['login'])): $sesh = $_SESSION['login'];?>
                <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown">
                    <i class="fas fa-user me-1"></i>
                <?php
                    $user_data = file_get_contents("http://$_SERVER[HTTP_HOST]/api/user?login=$sesh");
                    echo json_decode($user_data, true)['name'];
                ?></a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#" id="logoutBtn"><i class="fas fa-right-from-bracket me-1"></i>Odhlásit</a></li>
                    <script>
                        $('#logoutBtn').click(() => {
                            $.post("api/logout").always(() => location.reload());
                        })
                    </script>
                </ul>
                </div>
        <?php else: ?>
            <a class="nav-link navbar-text" href="/login" id="loginBtn"><i class="fas fa-user-lock me-1"></i>Přihlásit</a>
        <?php endif; ?>
    </div>
</nav>