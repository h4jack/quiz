<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include "./../../db/dbconn.php";
include "./../../method/string.php";


session_start();
$conn = connect_db("users");
$loggedin = false;
$error_msg = "";
$is_dberror = false;


function create_user(){
    global $conn, $emsg;
    $user_exists = false;
    if(isset($_POST['username'])){
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $name = trim(mysqli_real_escape_string($conn, trim($_POST['name'])));
        if(!is_uname($username)){
            $error_msg = "not a valid username.<br>make sure it starts with alphabet, and contain only  (a-z, 0-9, _), and length limit is 4-30.";
            return false;
        }
    
        //connect to database and create database with the table if not exists.
        $sql_query = "CREATE TABLE IF NOT EXISTS quizuser(
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(30) NOT NULL,
            password VARCHAR(30) NOT NULL,
            name VARCHAR(30),
            reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        if(!mysqli_query($conn, $sql_query)){
            $is_dberror = true;
            return false;
        }
    
        //check user exists or not.
        $sql_query = "SELECT username FROM quizuser WHERE username  = '$username';";
        $result = mysqli_query($conn, $sql_query);
        $nums = mysqli_num_rows($result);
        if($nums == 0){
            $user_exists = false;
        }elseif($nums > 1){
            $is_dberror = true; //multiple user fuond with same ..
            return false;
        }else{
            $user_exists = true;
        }
        
        //if user exists then try to log in or try to sign up.
        if($user_exists){
            //try to log in if match password.
            $sql_query = "SELECT * FROM quizuser WHERE username = '$username' AND password = '$password';";
            $result = mysqli_query($conn, $sql_query);
            $nums = mysqli_num_rows($result);
            if($nums == 0){
                $error_msg = "please don't try to access like this, it is unethical.";
                return false;
            }elseif($nums > 1){
                $is_dberror = true; //multiple user fuond with same username..
                return false;
            }elseif($nums == 1){
                $row = mysqli_fetch_assoc($result);
                $_SESSION['quiz_user_name'] = $row['username'];
                if($name != ""){
                    $sql_query = "UPDATE quizuser SET name='$name' WHERE username='$username'";
                    $result = mysqli_query($conn, $sql_query);
                    if(!$result){
                        $is_dberror = true;
                        return false;
                    }
                    $_SESSION['name'] = $name;
                }else{
                    $_SESSION['name'] = $row['name'];
                }
            }
        }else{
            //try to register if quiz not exists.
            $sql_query = "INSERT INTO quizuser (username, password, name) VALUES ('$username','$password','$name')";
            if(!mysqli_query($conn, $sql_query)){
                $is_dberror = true;
            }else{
                $_SESSION['quiz_user_name'] = $username;
                $_SESSION['name'] = $name;
            }
        }
    }
    return true;
}


if(create_user()){
    if(isset($_SESSION['quiz_user_name'])){
        $loggedin = true;
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
    <title>User Login</title>
    <link rel="stylesheet" href="/quiz/css/main.css">
    <link rel="stylesheet" href="/quiz/css/button.css">
    <link rel="stylesheet" href="/quiz/css/login.css">
    <link rel="stylesheet" href="/quiz/css/input.css">
</head>

<body>
    <div class="main">
        <div class='sign_div'>
            <h3 style='text-align:center;'>Login/Register to Quiz</h3>
            <?php
                if(!$loggedin){
                    if($error_msg != ""){
                        echo "<p style='color:red;text-align:center;'>$error_msg</p>";
                    }
                }else{
                    close_db($conn);
                    header("Location: ./../profile/");
                    exit;
                }
            ?>
            <form method='post' action=''>
            <label>Full Name: <input class="input_txt" type='name' name='name' placeholder='your Full Name.' maxlength="30"
                        minlength="3"></label>
                <label>Username: <input class="input_txt" type='text' name='username' placeholder='your Username' maxlength="30"
                        minlength="6" required></label>
                <label>Password: <input class="input_txt" type='password' name='password' placeholder='your Password' maxlength="30"
                        minlength="6" required></label>
                <input class="submit_btn" type='submit' value='Enter'>
            </form>
        </div>
    </div>
</body>

</html>