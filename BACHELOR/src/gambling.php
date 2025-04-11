<?php

session_start();
include_once 'db.php';
include_once 'header.php';

$db = db_connect();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
} else {
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

    if ($points >= $PRIZE_POINTS) {
        echo "Congratulations! You did a great job, get your prize <a class='btn btn-primary my-2 w-10' href='/prize.php'> here</a>!<br>";
    }
}




?>


<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="style.css">
<script src="script.js" defer></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

</div>
<body>
    <div id="headline">
        <h1>Dice Gambling</h1>
        <h2>Get 3 6's to double your bet, easy peasy</h2>
    </div>
    <div class="scoreContainer">
        <div id="score">
            <p id="scoreLine">Balance: <?php echo "$points"?></p>
        </div>
    </div>
    <div class="container">
        <div class="table">
            <div class="dice-container">
                <img src="img/1.png" id="dice1" class="dice" alt="dice1">
                <img src="img/2.png" id="dice2" class="dice" alt="dice2">
                <img src="img/3.png" id="dice3" class="dice" alt="dice3">
            </div>
        </div>
    </div>

    <div class="actionContainer">
        <input id="betInput" type="number"  name="Bet" placeholder="Bet">
        <button onclick="rollDices()" id="roleDiceButton">Roll</button>
    </div>
    
</body>
