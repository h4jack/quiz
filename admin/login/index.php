<?php 
include "./../../db/dbconn.php"; //include the database connect module which contain credentials.
include "./../../method/string.php";
//declare global variables...
//bool variables
$is_dberror = false;
$quiz_exists = false;
$error_msg = "";
$loggedin = FALSE;
//start the session for storing user log.
session_start();
$conn = connect_db("users");
function create_quiz(){
    global $conn, $error_msg, $is_dberror;
    if(isset($_POST['quizname']) && isset($_POST['password'])){
        $quizname = mysqli_real_escape_string($conn, $_POST['quizname']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $description = trim(mysqli_real_escape_string($conn, trim($_POST['description'])));
        if(!is_uname($quizname)){
            $error_msg = "quiz name is not valid.<br>make sure starts with alphabet, and contain only  (a-z, 0-9, _), and length limit is 4-30.";
            return false;
        }

        if(!table_exists("quizauth", $conn)){
            //connect to database and create database with the table if not exists.
            $sql_query = "CREATE TABLE quizauth(
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                quizname VARCHAR(30) NOT NULL,
                password VARCHAR(30) NOT NULL,
                description VARCHAR(200),
                reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";

            if(!mysqli_query($conn, $sql_query)){
                $is_dberror = true;
                return false;
            }
        }


        $sql_query = "SELECT quizname FROM quizauth WHERE quizname = '$quizname';";
        $result = mysqli_query($conn, $sql_query);
        $nums = mysqli_num_rows($result);
        if($nums == 0){
            $quiz_exists = false;
        }elseif($nums > 1){
            $is_dberror = true; //multiple user fuond with same ..
            return false;
        }else{
            $quiz_exists = true;
        }
        if($quiz_exists){
            //try to log in if match password.
            $sql_query = "SELECT * FROM quizauth WHERE quizname = '$quizname' AND password = '$password';";
            $result = mysqli_query($conn, $sql_query);
            $nums = mysqli_num_rows($result);
            if($nums == 0){
                $error_msg = "please don't try to access like this, it is unethical.";
                return false;
            }elseif($nums > 1){
                $is_dberror = true; //multiple user fuond with same quizname..
                return false;
            }elseif($nums == 1){
                $row = mysqli_fetch_assoc($result);
                $_SESSION['quizname'] = $row['quizname'];
                if($description != ""){
                    $sql_query = "UPDATE quizauth SET description='$description' WHERE quizname='$quizname'";
                    $result = mysqli_query($conn, $sql_query);
                    if(!$result){
                        $is_dberror = true;
                        return false;
                    }
                    $_SESSION['description'] = $description;
                }else{
                    $_SESSION['description'] = $row['description'];
                }
            }
        }else{
            //try to register if quiz not exists.
            $sql_query = "INSERT INTO quizauth (quizname, password, description) VALUES ('$quizname','$password','$description')";
            if(!mysqli_query($conn, $sql_query)){
                $is_dberror = true;
            }else{
                $_SESSION['quizname'] = $quizname;
                $_SESSION['description'] = $description;
            }
        }
    }
    close_db($conn);
    return true;
}

if(create_quiz()){
    if(isset($_SESSION['quizname'])){
        $loggedin = TRUE;
        $quizname = $_SESSION['quizname'];
        $description = $_SESSION['description'];
        //create edit and update questions..
    }
}else{
    if($is_dberror){
        echo "Error on Server Side.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Form</title>
    <link rel="stylesheet" href="/quiz/css/main.css">
    <link rel="stylesheet" href="/quiz/css/button.css">
    <link rel="stylesheet" href="/quiz/css/input.css">
    <link rel="stylesheet" href="/quiz/css/login.css">
</head>

<body>
    <div class="main">
        <?php
        if(!$loggedin){
            echo "
        <div class='sign_div' id='signin'>
            <h3 style='text-align:center;'>Create/Edit Quiz</h3>
            ";
            if($error_msg != ""){
                echo "<p style='color:red;text-align:center;'>$error_msg</p>";
            }
            echo "
            <form method='post' action=''>
                <label>Quiz name: <input class='input_txt' type='text' name='quizname' placeholder='enter quiz name.' maxlength='30' minlength='4' required></label>
                <label>Password: <input class='input_txt' type='password' name='password' placeholder='enter the password. required in future.' maxlength='30' minlength='6' required></label>
                <label>Description: <textarea class='input_txt' rows='10' name='description' maxlength='200' placeholder='ignore if you are logging in. or tell us something about this quiz (it is optional).'></textarea></label>
                <input class='submit_btn' type='submit' value='Submit'>
            </form>
        </div>";
        }else{
            if(isset($_POST['goto'])){
                $goto = $_POST['goto'];
                header("Location: $goto");
            }else{
                header("Location: ./../");
            }
            exit;
        }
        ?>
    </div>
</body>

</html>