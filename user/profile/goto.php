<?php
    include "../../db/dbconn.php";
    session_start();
    if(isset($_GET['quiz'])){
        $pattern = '/^[a-z][a-z0-9_]{3,29}$/i';

        if (preg_match($pattern, $_GET['quiz'])) {
            $_POST['quiz_search'] = $_GET['quiz'];
        } else {
            $_SESSION['error'] = "invalid quiz name.";
            header("Location: ./");
            exit;
        }
    }
    if(isset($_POST['quiz_search'])){
        $conn = connect_db('quiz');
        $quizname = mysqli_real_escape_string($conn, $_POST['quiz_search']);
        if(table_exists($quizname, $conn)){
            $_SESSION['user_quiz_name'] = $quizname;
            close_db($conn);
            $conn = connect_db('users');
            $sql_query = "SELECT quizname, description FROM quizauth
                        WHERE quizname='$quizname'";
            $result = mysqli_query($conn, $sql_query);
            close_db($conn);
            if(mysqli_num_rows($result) == 1){
                $row = mysqli_fetch_assoc($result);
                $_SESSION['user_quiz_name'] = $row['quizname'];
                $_SESSION['user_quiz_des'] = $row['description'];
                header("Location: ../getquiz");
            }else{
                $_SESSION['error'] = "quiz doesn't exists at all. please connect to the quiz creator.";
                header("Location: ./");
            }
        }else{
            $_SESSION['error'] = "quiz doesn't exists at all. please recheck the quiz name.";
            header("Location: ./");
        }
        exit;
    }
?>