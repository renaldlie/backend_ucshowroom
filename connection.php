<?php

    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "ucshowroom";

    $connect = mysqli_connect($hostname, $username, $password, $database);

    if(!$connect) {
        die("Failed connecting to database: ".mysqli_connect_error());
    }

