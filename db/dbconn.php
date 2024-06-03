<?php
function connect_db($db_name){
    $db_servername = "localhost"; //your detabase server url;
    $db_username = "root";  //your sql database username;
    $db_password = "";      //your sql database password;
    $conn = mysqli_connect($db_servername, $db_username, $db_password);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
        return 0;
    }
    
    $create_db = "CREATE DATABASE if not exists $db_name";
    if (mysqli_query($conn, $create_db)) {
        return mysqli_connect($db_servername, $db_username, $db_password, $db_name);
    } else {
        return 0;
    }
}

function table_exists($table_name, $db_connection) { //check if table is exists on the connected databases or not.
    $sql = "SHOW TABLES LIKE '{$table_name}'";
    $result = mysqli_query($db_connection, $sql);
  
    return mysqli_num_rows($result) > 0;
}

function close_db($conn){
    mysqli_close($conn);
}
?>