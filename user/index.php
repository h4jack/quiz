<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include "./../db/dbconn.php";
include "./../div/nav.php";
include "./../submit/index.php";

$quizname = "";
$description = "";

$total_quiz = "";
$question_id = ""; 
$question = ""; 
$opt1 = "";
$opt2 = ""; 
$opt3 = "";
$opt4 = "";
$given_answer = "";
$given_answer_past = "";
$opt1_class = "";
$opt2_class = "";
$opt3_class = "";
$opt4_class = "";


session_start();

if(isset($_SESSION['quiz_user_name'])){
    $loggidin = true;
    $username = $_SESSION['quiz_user_name'];
    $name = $_SESSION['name'];
}else{
    $loggidin = false;
}
if(!$loggidin || isset($_POST['logout'])){
    unset($_SESSION['quiz_user_name']);
    unset($_SESSION['name']);
    unset($_SESSION['user_quiz_name']);
    unset($_SESSION['user_quiz_des']);
    header("Location: ./login/");
    exit;
}
if(isset($_SESSION['user_quiz_name'])){
    $quizname = $_SESSION['user_quiz_name'];
    $description = $_SESSION['user_quiz_des'];
    $table_name = $quizname . "_" . $username;


    create_result_table();
    if(row_exists($username, $quizname)){
        if(is_submit()){
            $_SESSION['error'] = "You have submited that quiz. you can't retake that quiz.";
            header("Location: ./profile/");
            exit;
        }else{
            if(isset($_POST['submit'])){
                set_true();
                header("Location: .");
            }
        }
    }else{
        set_false();
    }


    if(isset($_POST['question_id'])){
        $question_id = $_POST['question_id'];
    }elseif(isset($_SESSION['question_id'])){
        $question_id = $_SESSION['question_id'];
        unset($_SESSION['question_id']);
    }else{
        $question_id = 1;
    }
    
    $conn = connect_db('result');

    if(!table_exists($table_name, $conn)){
        header("Location: ./getquiz");
        exit;
    }
    $sql_query = "SELECT * FROM $table_name";
    $total_quiz = mysqli_num_rows(mysqli_query($conn, $sql_query));
    $sql_query = "SELECT * FROM $table_name
                WHERE id=$question_id";
    $result = mysqli_query($conn, $sql_query);
    if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_assoc($result);
        $question = htmlspecialchars_decode($row['question']);
        $given_answer_past = htmlspecialchars_decode($row['given_answer']);
        $opt1 = htmlspecialchars_decode($row['opt1']);
        $opt2 = htmlspecialchars_decode($row['opt2']);
        $opt3 = htmlspecialchars_decode($row['opt3']);
        $opt4 = htmlspecialchars_decode($row['opt4']);
    }else{
        echo "Error No Quiz Question Found with that Question id";
        exit;
    }
}else{
    header("Location: ./profile");
    exit;
}

if(isset($_POST['given_answer'])){
    $given_answer = htmlspecialchars($_POST['given_answer']);

    $sql_query = "SELECT * FROM $table_name WHERE id=$question_id";
    $result = mysqli_query($conn, $sql_query);
    $num = mysqli_num_rows($result);
    if($num == 1){
        //update the values;
        if($given_answer == $given_answer_past){
            $given_answer = "";
            $given_answer_past = "";
            $_SESSION['question_id'] = $question_id;
            header("Location: ./goto.php");
        }else{
            $given_answer_past = $given_answer;
        }
        $sql_query = "UPDATE $table_name SET given_answer='". htmlspecialchars(mysqli_real_escape_string($conn, $given_answer)) ."' WHERE id=$question_id";
        if(!mysqli_query($conn, $sql_query)){
            echo "Answer Updation Error!";
            exit;
        }
    }else{
        echo "Can't Update you'r answer.";
        exit; 
    }
}

close_db($conn);

switch($given_answer_past){
    case $opt1:
        $opt1_class = "class='checked'";
        break;
    case $opt2:
        $opt2_class = "class='checked'";
        break;
    case $opt3:
        $opt3_class = "class='checked'";
        break;
    case $opt4:
        $opt4_class = "class='checked'";
        break;
    default:
        $opt1_class = "";
        $opt2_class = "";
        $opt3_class = "";
        $opt4_class = "";
}

// Username : username
// Name : name

// Quiz Name : quizname
// Quiz Description : description

// Total No of Question : total_quiz
// Question id : qid;
// Question : question
// Option 1 : opt1
// Option 2 : opt2
// Option 3 : opt3
// Option 4 : opt4
// Given Answer : given_answer

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $quizname; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="./../css/nav.css">
    <link rel="stylesheet" href="./../css/main.css">
    <link rel="stylesheet" href="./../css/body.css">
    <style>
    .body_left {
        width: 30%;
    }

    .body_right {
        width: 100%;
    }

    @media (max-width: 600px) {
        .question_no {
            display: none;
        }

        .body_left {
            width: 100%;
            height: auto;
            min-height: auto;
            margin: 0px;
            padding: 0px;
        }

        .body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .questions {
            width: 90%;
        }
    }
    </style>
</head>

<body>
    <div class="main">
        <?php
            //echo the nav div from nav/nav.php
            echo_nav($name, $username,"Name","Username","./profile");
        ?>
        <div class="body">
            <div id="show_qno" class="body_left">
                <h2>Question No</h2>
                <div id="question_no" class="question_no">
                    <?php
                    if($total_quiz != ""){
                        for($i = 1; $i <= $total_quiz; $i++){
                            if($i == $question_id){
                                echo "
                    <form action='' method='post'>
                        <input type='hidden' name='question_id' value='$i'>
                        <input type='submit' class='checked' value='$i'>
                    </form>";
                    }else{
                        echo "
                    <form action='' method='post'>
                        <input type='hidden' name='question_id' value='$i'>
                        <input type='submit' value='$i'>
                    </form>";
                            }
                        }
                    }else{
                        echo "
                    <p>No Value Present</p>";
                    }
                    ?>
                </div>
            </div>
            <div class="body_right">
                <div class="quiz_heading">
                    <?php
                        echo "<h2>Quiz Name: $quizname</h2>";
                        if($description != ""){
                            echo "<p>Description: $description</p>";
                        }else{
                            echo "<p>No additional information available for this quiz.</p>";
                        }
                    ?>
                </div>
                <div class="questions">
                    <?php
                    echo "
                        <div>
                            <h2>$question_id. </h2>
                            <h2>$question</h2>
                        </div>";
                    echo "
                    <form action='' method='post'>
                        <input type='hidden' name='question_id' value='$question_id'>
                        <input type='hidden' name='given_answer' value='$opt1'>
                        <input type='submit' id='opt1' value='A. $opt1' $opt1_class>
                    </form>";
                        echo "
                    <form action='' method='post'>
                        <input type='hidden' name='question_id' value='$question_id'>
                        <input type='hidden' name='given_answer' value='$opt2'>
                        <input type='submit' id='opt2' value='B. $opt2' $opt2_class>
                    </form>";
                        if($opt3 != ""){
                            echo"
                    <form action='' method='post'>
                        <input type='hidden' name='question_id' value='$question_id'>
                        <input type='hidden' name='given_answer' value='$opt3'>
                        <input type='submit' id='opt3' value='C. $opt3' $opt3_class>
                    </form>";
                        }
                        if($opt4 != ""){
                            echo"
                    <form action='' method='post'>
                        <input type='hidden' name='question_id' value='$question_id'>
                        <input type='hidden' name='given_answer' value='$opt4'>
                        <input type='submit' id='opt4' value='D. $opt4' $opt4_class>    
                    </form>";
                    }
                    ?>
                </div>
                <div class="bottom">
                    <?php
                    if($question_id <= 1){
                        $prev = 1;
                    }else{
                        $prev = $question_id-1;
                    }
                    if($question_id >= $total_quiz){
                        $next = $total_quiz;
                    }else{
                        $next = $question_id+1;
                    }

                        echo"
                    <form action='' method='post'>
                        <input type='hidden' name='question_id' value='$prev'>
                        <input id='previous' type='submit' value='Previous'>
                    </form>
                    <form action='' method='post'>
                        <input type='hidden' name='submit'>
                        <input id='submit' type='submit' value='Submit'>
                    </form>
                    <form action='' method='post'>
                        <input type='hidden' name='question_id' value='$next'>
                        <input id='next' type='submit' value='Next'>
                    </form>";
                    ?>
                </div>
            </div>
        </div>
    </div>
    <script>
    const show_qno = document.getElementById("show_qno");
    const qno = document.getElementById("question_no");

    show_qno.onclick = function() {
        const isnone = (qno.style.display === 'none');
        if (innerWidth < 601) {
            qno.style.display = isnone ? 'flex' : 'none';
        } else {
            qno.style.display = 'flex';
        }
    };
    window.addEventListener('resize', function() {
        if (innerWidth > 600) {
            qno.style.display = 'flex';
        } else {
            qno.style.display = 'none';

        }
    });
    </script>
</body>

</html>