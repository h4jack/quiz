<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include "../../db/dbconn.php";
include "../../submit/index.php";

function error_occured($error_msg){
    echo "<p>" . $error_msg . "</p>
<form action='../profile' method='post'>
    <input type='submit' value='Go to HomePage'>
</form>";
    exit;
}

session_start();

if(isset($_SESSION['user_quiz_name']) && isset($_SESSION['quiz_user_name'])){
    $username = $_SESSION['quiz_user_name'];
    $user_quiz_name = $_SESSION['user_quiz_name'];
    
    create_result_table();
    if(row_exists($username, $user_quiz_name)){
        if(is_submit()){
            $_SESSION['error'] = "You have submited that quiz. you can't retake that quiz.";
            header("Location: ./../profile/");
            exit;
        }
    }else{
        set_false();
    }

    $conn = connect_db("quiz");
    $sql_query = "SELECT * FROM $user_quiz_name";
    $result = mysqli_query($conn, $sql_query);
    close_db($conn);

    $conn = connect_db('result');
    if(mysqli_num_rows($result)){
        $table_name = $user_quiz_name . "_" . $username;

        if(!table_exists($table_name, $conn)){
            $sql_query = "CREATE TABLE IF NOT EXISTS $table_name(
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            question VARCHAR(500) NOT NULL,
            answer VARCHAR(200) NOT NULL,
            given_answer VARCHAR(200),
            opt1 VARCHAR(200) NOT NULL,
            opt2 VARCHAR(200) NOT NULL,
            opt3 VARCHAR(200),
            opt4 VARCHAR(200),
            reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            up_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            if(!mysqli_query($conn, $sql_query)){
                error_occured("Can't complete the request. error from server side");
            }
        }
        
        while($row = mysqli_fetch_assoc($result)){
            $question_id = $row['id'];
            $question = $row['question'];
            $answer = $row['answer'];
            $given_answer = "";
            if ($row['opt3'] != "" && $row['opt4'] != "") {
                $option = array($row['opt1'], $row['opt2'], $row['opt3'], $row['opt4']);
                shuffle($option);
                list($opt1, $opt2, $opt3, $opt4) = $option;
            } elseif ($row['opt3'] != "") {
                $option = array($row['opt1'], $row['opt2'], $row['opt3']);
                shuffle($option);
                list($opt1, $opt2, $opt3) = $option;
                $opt4 = "";
            } elseif ($row['opt4'] != "") {
                $option = array($row['opt1'], $row['opt2'], $row['opt4']);
                shuffle($option);
                list($opt1, $opt2, $opt4) = $option;
                $opt3 = "";
            } else {
                $option = array($row['opt1'], $row['opt2']);
                shuffle($option);
                list($opt1, $opt2) = $option;
                $opt3 = $opt4 = "";
            }
            
            $sql_query = "SELECT * FROM $table_name WHERE id=$question_id";
            $result_two = mysqli_query($conn, $sql_query);
            $num = mysqli_num_rows($result_two);
            if($num == 1){
                //table exist and the row / question exists already that is why updating.
                $sql_query = "UPDATE $table_name SET
                question = '". htmlspecialchars(mysqli_real_escape_string($conn, $question)). "',
                answer = '". htmlspecialchars(mysqli_real_escape_string($conn, $answer)). "',
                opt1 = '". htmlspecialchars(mysqli_real_escape_string($conn, $opt1)). "',
                opt2 = '". htmlspecialchars(mysqli_real_escape_string($conn, $opt2)). "',
                opt3 = '". htmlspecialchars(mysqli_real_escape_string($conn, $opt3)). "',
                opt4 = '". htmlspecialchars(mysqli_real_escape_string($conn, $opt4)). "'
                WHERE id=$question_id";
                if(!mysqli_query($conn, $sql_query)){
                    error_occured("Answer Updation Error!");
                }
            }elseif($num == 0){
                //insert the values;
                $sql_query = "INSERT INTO $table_name(id, question, answer, given_answer, opt1, opt2, opt3, opt4)
                VALUES($question_id,
                '". htmlspecialchars(mysqli_real_escape_string($conn, $question)). "',
                '". htmlspecialchars(mysqli_real_escape_string($conn, $answer)). "',
                '',
                '". htmlspecialchars(mysqli_real_escape_string($conn, $opt1)). "',
                '". htmlspecialchars(mysqli_real_escape_string($conn, $opt2)). "',
                '". htmlspecialchars(mysqli_real_escape_string($conn, $opt3)). "',
                '". htmlspecialchars(mysqli_real_escape_string($conn, $opt4)). "')";
                if(!mysqli_query($conn, $sql_query)){
                    error_occured("Can't Update you'r answer.");
                }
            }else{
                error_occured("We Discovered a critical Error!");
            }
        }
        close_db($conn);
        header("Location: ../");
        exit;
    }else{
        $_SESSION['error'] = "the quiz is incomplete (no question found).";
        header("Location: ./../profile");
    }
}else{
    header("Location: ./../login/");
    error_occured("Error in this page. somehow it can't complete it's job.");
}

// $_SESSION['user_quiz_name'] = "";
// $_SESSION['user_quiz_des'] = "";

// $_SESSION['total_quiz'] = "";
// $_SESSION['question_id'] = "";
// $_SESSION['question'] = "";
// $_SESSION['answer'] = "";
// $_SESSION['opt1'] = "";
// $_SESSION['opt2'] = "";
// $_SESSION['opt3'] = "";
// $_SESSION['opt4'] = "";
// $_SESSION['given_answer'] = "";
?>