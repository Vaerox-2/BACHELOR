<?php

session_start();

include_once 'db.php';

$db = db_connect();

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
                if (isset($_POST['points'])) {
                    //add points
                    $addpoints = $_POST['points'];
                    $totalpoints = $addpoints + $points;
                    $sql = $db->prepare("UPDATE points set points = ? where id=?");
                    $sql->bind_param("is", $totalpoints, $email);
                    if (!$sql->execute()) {
                        //error updating points
                        $sql->close();
                        $db->close();
                        exit();
                    } else {
                    }
                } else {
                    //get point total
                    echo "$points";                        
                }
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

?>



