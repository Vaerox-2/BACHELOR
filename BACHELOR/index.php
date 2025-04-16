<?php
session_start();
include_once 'header.php';
?>

<div class="container my-5">
    <h1 class="text-center">Welcome to Dice Gambling</h1>
    <p class="text-center mb-4">Get 3 6's to double your bet, easy peasy</p>

    <?php
    if (isset($_SESSION['user'])) {
        echo '<a href="gambling.php" class="btn btn-primary">Start Gambling</a>';
    } else {
        echo '<a href="login.php" class="btn btn-primary">Login to start</a>';
    }
    ?>
