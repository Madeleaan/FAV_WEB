<nav class="navbar navbar-expand-lg bg-body-tertiary px-3 border-bottom mb-3">
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
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            <?php
            if (!empty($_SESSION['login'])):
                $api = new API();
                $user = $api->getUser($_SESSION['login']);?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>
                    <?php echo htmlspecialchars($user->name) ?></a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item">
                            <i class="fas fa-medal me-1"></i>Role: <?php echo Role::str($user->role) ?></a>
                        </a></li>
                        <?php if ($user->role == Role::AUTHOR): ?>
                            <li><a class="dropdown-item" href="/author/articles">
                                <i class="fas fa-newspaper me-1"></i>Moje články
                            </a></li>
                        <?php elseif ($user->role >= Role::ADMIN): ?>
                            <li><a class="dropdown-item" href="/admin/usercontrol">
                                <i class="fas fa-users me-1"></i>Správa uživatelů
                            </a></li>
                            <li><a class="dropdown-item" href="/admin/articles">
                                    <i class="fas fa-list-ol me-1"></i>Správa článků
                                </a></li>
                        <?php endif ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" id="logoutBtn">
                            <i class="fas fa-right-from-bracket me-1"></i>Odhlásit
                        </a></li>
                        <script>
                            $('#logoutBtn').click(() => {
                                $.post("/api/logout", () => location.reload())
                            })
                        </script>
                    </ul>
                </li>
            <?php else: ?>
                <a class="nav-link navbar-text" href="/login" id="loginBtn"><i class="fas fa-user-lock me-1"></i>Přihlásit</a>
            <?php endif; ?>
        </ul>
    </div>
</nav>