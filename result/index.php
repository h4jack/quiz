<?php
include "./../db/dbconn.php";
include "./../div/nav.php";


session_start();
$loggedin = false;

if(isset($_SESSION['post_type'])){
    $_POST['post_type'] = $_SESSION['post_type'];
    unset($_SESSION['post_type']);
}

if(isset($_POST['post_type'])){
    $post_type = $_POST['post_type'];
    if($post_type == "user"){
        if(isset($_SESSION['user_quiz_result_name']) && isset($_SESSION['quiz_user_name'])){
            $username = $_SESSION['quiz_user_name'];
            $quizname = $_SESSION['user_quiz_result_name'];
            $t1 = $_SESSION['name'];
            $t2 = $username;
            $t11 = "Name";
            $t22 = "Username";
            $rdlink = "./../user/profile";
        }else{
            $_SESSION['error'] = "result doesn't found with that username. <br> try to submit again, if not submitted. <br> or else contect support.";
            header("Location: ../user/profile");
            exit;
        }
    }elseif($post_type == "admin"){
        if(isset($_SESSION['quiz_user_result_name']) && isset($_SESSION['quizname'])){
            $username = $_SESSION['quiz_user_result_name'];
            $quizname = $_SESSION['quizname'];
            $t1 = $_SESSION['description'];
            $t2 = $quizname;
            $t11 = "Quizname";
            $t22 = "Description";
            $rdlink = "./../admin/";
        }else{
            header("Location: ../admin/");
            exit;
        }
    }else{
        header("Location: ../");
        echo "Oops you mislinked";
        exit;
    }
    $conn = connect_db('result');
    $table_name = $quizname . "_" . $username;
    unset($_SESSION['user_quiz_result_name']);
    if(!table_exists($table_name, $conn)){
        $_SESSION['error'] = "result doesn't found with that username. <br> try to submit again, if not submitted. <br> or else contect support.";
        header("Location: ../user/profile");
        exit;
    }
    $sql_query = "SELECT * FROM $table_name";
    $total_question = mysqli_num_rows(mysqli_query($conn, $sql_query));
    
    $sql_query = "SELECT * FROM $table_name WHERE given_answer!=''";
    $question_attempted = mysqli_num_rows(mysqli_query($conn, $sql_query));
    
    $sql_query = "SELECT * FROM $table_name WHERE given_answer=answer";
    $correct_answer = mysqli_num_rows(mysqli_query($conn, $sql_query));
    
    $wrong_answer = $question_attempted-$correct_answer;
    
    $total_marks = $correct_answer;
    $negetive_marks = $correct_answer-($wrong_answer*0.25);
}else{
    header("Location: ../");
    echo "Oops you mislinked";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo "Result for ". $quizname?></title>
    <link rel="stylesheet" href="../user/style.css">
    <link rel="stylesheet" href="./../css/nav.css">
    <link rel="stylesheet" href="./../css/main.css">
    <link rel="stylesheet" href="./../css/body.css">
    <link rel="stylesheet" href="/quiz/css/table.css">
    <style>
    .body_left {
        width: 30%;
    }
    .body_right {
        width: 100%;
        height: 500px;
    }

    .questions{
        overflow-y: auto;
        align-content: center;
        text-align: center;
        justify-content: center;
        width:auto;
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

    .table {
        width: 100%;
        border-collapse: collapse;
        border: none;
        padding: 20px;
    }

    .table tr td {
        padding: 8px;
        text-align: left;
    }
    </style>
</head>

<body>
    <div class="main">
        <?php
            //echo the nav div from nav/nav.php
            echo_nav($t1, $t2,$t11,$t22,$rdlink);
        ?>
        <div id="section-to-print" class="body">
            <div id="show_qno" class="body_left">
                <h3>Scores</h3>
                <div class="questions">

                    <table class="table">
                        <?php
                        echo "
                        <tr>
                            <td>total question</td>
                            <td>$total_question</td>
                        </tr>
                        <tr>
                            <td>question attempted</td>
                            <td>$question_attempted</td>
                        </tr>
                        <tr>
                            <td>correct questions</td>
                            <td>$correct_answer</td>
                        </tr>
                        <tr>
                            <td>wrong answers</td>
                            <td>$wrong_answer</td>
                        </tr>
                        <tr>
                            <td>total marks</td>
                            <td>$total_marks/$total_question</td>
                        </tr>
                        <tr>
                            <td>Total with Negetive</td>
                            <td>$negetive_marks/$total_question</td>
                        </tr>"    
                    ?>
                    </table>
                </div>
            </div>
            <div class="body_right">
                <?php
                echo"
                <h2>Quiz Result View for the $quizname</h2>";
                ?>
                <div class='questions'>
                    <table class='q_table'>
                        <thead>
                            <tr>
                                <th>SL NO</th>
                                <th>Question</th>
                                <th>Given Answer</th>
                                <th>Correct Answer</th>
                            </tr>
                        </thead>
                        <?php
                    $sql_query = "SELECT * FROM $table_name";
                    $result = mysqli_query($conn, $sql_query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        $id = html_entity_decode($row['id']);
                        $question = html_entity_decode($row['question']);
                        $given_answer = html_entity_decode($row['given_answer']);
                        $correct_answer = html_entity_decode($row['answer']);
                        echo "
                        <tbody>
                            <tr>
                                <td>$id</td>
                                <td>$question</td>";
                        if($given_answer == $correct_answer){
                            //correct answer
                            echo "<td style='background-color:green;'>$given_answer</td>";
                        }elseif($given_answer == ""){
                            //not answered
                            echo "<td style='background-color:gray;'>Not Answered</td>";
                        }else{
                            //wrong answered
                            echo "<td style='background-color:red;'>$given_answer</td>";
                        }
                        echo "
                                <td>$correct_answer</td>
                            </tr>
                        </tbody>";
                    }
                ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>