<?php

session_start();

include_once 'db.php';
include_once 'header.php';

$db = db_connect();
$points = -1;

if (!isset($_SESSION['user'])) {
    echo "<meta http-equiv='refresh' content='0;url=login.php'>";
    $db->close();
    exit();
} else {
    $userid = $_SESSION['user'];
    $sql = "SELECT * FROM users WHERE id = '$userid'";
    $result = $db->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];
        $sql = "SELECT * FROM points WHERE id = '$email'";
        $result = $db->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $points = $row['points'];
        } else {
            echo "error fetching points, please refresh";
            session_destroy();
            $db->close();
            exit();    
        }
    } else {
        echo "error finding user, please refresh";
        session_destroy();
        $db->close();
        exit();
    }
}


echo "<h3>Your point total: $points</h3>";


?>



