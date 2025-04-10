<?php

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

include_once 'db.php';
include_once 'header.php';

$db = db_connect();

$user = $_SESSION['user'];

$sql = "SELECT * FROM users WHERE id = '$user'";
$result = $db->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = $row['email'];
    $sql2 = "SELECT * FROM points WHERE id = '$username'";
    $result = $db->query($sql2);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $points = $row['points'];
    } else {
        echo "Please refresh the page";
        session_destroy();
        $db->close();
        exit();
    }
} else {
    echo "Please refresh the page";
    session_destroy();
    $db->close();
    exit();
}


if ($points < $PRIZE_POINTS) {
    echo "<p>Your balance is $points</p><p>You need a balance of at least $PRIZE_POINTS to get the prize :/</p>";
} else {
    $TOKEN = "Flag: Bachelor(G4MBLING_BL1NG)";
    file_put_contents("/prizes/$user.txt",$TOKEN);
}

if (file_exists("/prizes/$user.txt")) {
    echo "Congratulations, you won!<br>Here's your throphy<br>";
    $flag = file_get_contents("/prizes/$user.txt");
    echo "$flag";   
} else {
    echo '<a class="btn btn-primary mt-3" href="/gambling.php">Play the game</a>';
}