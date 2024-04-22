<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-primary py-3">
        <div class="container-fluid">
            <!-- TITOLO -->
            <a class="navbar-brand text-white fw-bolder fs-2" href="/php-blog/index.php">My Blog</a>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <?php if (isset($_SESSION['username'])) { ?>
                        <!-- SE L'UTENTE E' LOGGATO -->
                        <li class="nav-item me-2">
                            <span class="navbar-text d-inline-block me-2 text-white fw-bolder">Benvenuto,
                                <span class="fst-italic"><?php echo $_SESSION['username']; ?></span>
                            </span>
                        </li>
                        <li class="nav-item me-2">
                            <a class="btn btn-light fw-bolder" href="/php-blog/posts/index.php">MY POST</a>
                        </li>
                        <li class="nav-item me-2">
                            <a class="btn btn-light fw-bolder" href="/php-blog/auth/logout.php">LOGOUT</a>
                        </li>
                    <?php } else { ?>
                        <!-- sE L'UTENTE NON E' LOGGATO -->
                        <li class="nav-item me-2">
                            <a class="btn btn-light" href="/php-blog/auth/login.php">LOGIN</a>
                        </li>
                        <li class="nav-item me-2">
                            <a class="btn btn-light" href="/php-blog/auth/register.php">REGISTER</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>
</header>