<?php


$PRIZE_POINTS = 3000;


function db_connect() {
    // Connect to the database
    $username = 'root';
    $password = 'password';
    $host = $_ENV['DB_HOST'];
    
    $conn = new mysqli($host, $username, $password, "db");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}