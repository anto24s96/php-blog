<?php
session_start();

// Unset della variabile di sessione e distruzione della sessione
unset($_SESSION['username']);
session_destroy();

// Redirect all'index
header("Location: /php-blog/index.php");
exit();
