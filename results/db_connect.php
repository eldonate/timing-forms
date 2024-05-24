<?php

include 'api_config.php';

function connect_to_db() {
    global $db_config;

    $conn = mysqli_connect(
        $db_config['host'], 
        $db_config['username'], 
        $db_config['password'], 
        $db_config['dbname']
    );

    if (!$conn) {
        echo "Error: Could not connect to database. " . mysqli_connect_error();
        exit;
    }

    return $conn;
}
