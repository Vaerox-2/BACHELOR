<?php

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: /');
    exit();
}

include_once 'db.php';
include_once 'header.php';

    
session_destroy();

echo "you have been logged out!<br>";
echo "<a class='btn btn-primary mt-3' href='/index.php'>Start Gambling</a>";

?>



