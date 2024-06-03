<?php 
//start the session for storing user log.
include "./../db/dbconn.php";
include "./../div/nav.php";

function derror(){
    unset($_SESSION['quizname']);
    unset($_SESSION['description']);
    echo "Sorry You have been logged out. <br> There is server Error.";
    exit;
}



session_start();
$quizname = "";
$description = "";
$loggedin = false;
$conn = connect_db("quiz");
if(isset($_SESSION['quizname'])){
    $quizname = $_SESSION['quizname'];
    $description = $_SESSION['description'];
    $loggedin = true;
    $sql_query = "CREATE TABLE IF NOT EXISTS $quizname(
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        question VARCHAR(500) NOT NULL,
        answer VARCHAR(200) NOT NULL,
        opt1 VARCHAR(200) NOT NULL,
        opt2 VARCHAR(200) NOT NULL,
        opt3 VARCHAR(200),
        opt4 VARCHAR(200),
        reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        up_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    if(!mysqli_query($conn, $sql_query)){
        derror();
    }
}
if(isset($_POST['logout'])){
    $loggedin = false;
}
if(isset($_POST['submits'])){
    header("Location: ./submits/");
}
?>
<?php
if(isset($_POST['dropid'])){
    //delete/drop the column which user want to delete?
    $dropid = $_POST['dropid'];
    if($quizname == ""){
        derror();
    }
    $stmt = mysqli_prepare($conn, "DELETE FROM $quizname WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $dropid);
    mysqli_stmt_execute($stmt); 
    if(mysqli_stmt_affected_rows($stmt) <= 0){
        derror();
    }else {
        // update the id values of the rows below the deleted row
        $sql_query = "UPDATE $quizname SET id = id - 1 WHERE id > $dropid";
        if(!mysqli_query($conn, $sql_query)){
            derror();
        }
    }
}

if(isset($_POST['ids'])){
    $ids = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['ids']));
    $questions = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['questions']));
    $answers = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['answers']));
    $opt1s = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['opt1s']));
    $opt2s = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['opt2s']));
    $opt3s = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['opt3s']));
    $opt4s = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['opt4s']));
    $is_edit = "Update";
}else{
    $ids = "";
    $questions = "";
    $answers = "";
    $opt1s = "";
    $opt2s = "";
    $opt3s = "";
    $opt4s = "";
    $is_edit = "Create";
}
if(isset($_POST['id'])){
    $id = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['id']));
    $question = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['question']));
    $answer = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['answer']));
    $opt1 = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['opt1']));
    $opt2 = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['opt2']));
    $opt3 = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['opt3']));
    $opt4 = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['opt4']));
    if($id != ""){
        //update data
        $stmt = mysqli_prepare($conn, "UPDATE $quizname SET question=?, answer=?, opt1=?, opt2=?, opt3=?, opt4=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssssssi", $question, $answer, $opt1, $opt2, $opt3, $opt4, $id);
        mysqli_stmt_execute($stmt);

        if(mysqli_stmt_affected_rows($stmt) <= 0) {
            // handle error
            derror();
        }
    }else{
        // Get the latest id from the table
        $sql_query = "SELECT MAX(id) AS max_id FROM $quizname";
        $result = mysqli_query($conn, $sql_query);
        $row = mysqli_fetch_assoc($result);
        $next_id = $row['max_id'] + 1;

        // Insert the new record with the next id
        $sql_query = "INSERT INTO $quizname (id, question, answer, opt1, opt2, opt3, opt4)
                      VALUES ($next_id, '$question', '$answer', '$opt1', '$opt2', '$opt3', '$opt4')";
        $result = mysqli_query($conn, $sql_query);
        if(!$result){
            derror();
        }
    }
    header("Location: ./");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Quiz</title>
    <link rel="stylesheet" href="/quiz/css/admin.css">
    <link rel="stylesheet" href="/quiz/css/main.css">
    <link rel="stylesheet" href="/quiz/css/nav.css">
    <link rel="stylesheet" href="/quiz/css/body.css">
    <link rel="stylesheet" href="/quiz/css/button.css">
    <link rel="stylesheet" href="/quiz/css/input.css">
    <style>
    .body_left {
        width: 40%;
    }


    .body_right {
        width: 60%;
        height: 550px;
        padding-bottom: 0px;
    }

    @media (max-width: 600px) {
        .body_left {
            width: 100%;
            height: auto;
            min-height: auto;
            margin: 0px;
            padding: 0px;
            padding-bottom: 10px;
        }

        .body_right {
            width: 100%;
            height: auto;
            min-height: auto;
            margin: 0px;
            margin-top: 10px;
            padding: 0px;
            overflow-x: auto;
            text-align: center;
        }

        .body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
    }
    </style>
</head>

<body>
    <div class="main">
        <?php
        if(!$loggedin){
            unset($_SESSION['quizname']);
            header("Location: ./login/");
            exit;
        }else{
            $url = $_SERVER['SERVER_NAME'];
            //echo the nav div from nav/nav.php
            echo "        <div class='nav'>
            <a style='text-decoration:none; color: #F5E8C7;' href='/quiz/user/profile/goto.php?quiz=$quizname'><div class='username'>
                <h2>Quizname : $quizname</h2>";
                if(trim($description) != ""){
                    echo "
                    <p>Description : $description</p>";
                }
                echo "
                <p>Quiz Link : $url/quiz/user/profile/goto.php?quiz=$quizname</p>
            </div></a>
            <div class='logout'>
                <form action='' method='post'>
                    <input type='hidden' name='submits' value='submits'>
                    <input type='submit' style='background-color:green;' value='View Submits'>
                </form>
                <form style='width:10px;' ></form>
                <form action='' method='post'>
                    <input type='hidden' name='logout' value='logout'>
                    <input type='submit' value='Log Out'>
                </form>
            </div>
        </div>";
            echo "
        <div class='body'>
            <div class='body_left'>
                <h3 style='text-align:center;'>Enter Questions</h3>
                <form class='question_form' method='post' action=''>
                    <input type='hidden' name='id' value='$ids'>
                    <label>Question: <textarea class='input_txt' rows='3' name='question' maxlength='500' minlength='1' required>$questions</textarea></label>
                    <label>Answer: <input class='input_txt' type='text' name='answer' maxlength='200' minlength='1' id='input_answer' value='$answers' required></label>
                    <label>Option 1: <input class='input_txt' type='text' name='opt1' maxlength='200' minlength='1' id='input_option1' value='$opt1s' required readonly></label>
                    <label>Option 2: <input class='input_txt' type='text' name='opt2' maxlength='200' minlength='1' value='$opt2s' required></label>
                    <label>Option 3: <input class='input_txt' type='text' name='opt3' maxlength='200' minlength='' value='$opt3s' ></label>
                    <label>Option 4: <input class='input_txt' type='text' name='opt4' maxlength='200' minlength='' value='$opt4s' ></label>
                    <input class='submit_btn' type='submit' value='$is_edit'>
                </form>";
            if($is_edit == "Update"){
                echo "
                <form class='question_form' action='' method='post'>
                    <input class='cancel_btn' type='submit' value='Cancel'>
                </form>";
            }
            echo "
            </div>
            <div class='body_right'>
                <h2>Your Questions for the Quiz myquiz</h2>
                <div class='questions'>
                    <table class='q_table'>
                        <thead>
                            <tr>
                                <th>SL NO</th>
                                <th>Question</th>
                                <th>Answer</th>
                                <th>Option-1</th>
                                <th>Option-2</th>
                                <th>Option-3</th>
                                <th>Option-4</th>
                                <th>Click</th>
                            </tr>
                        </thead>";
            $sql_query = "SELECT * FROM $quizname";
            $result = mysqli_query($conn, $sql_query);
            if(!$result){
                session_destroy();
                echo "Sorry You have been logged out. <br> There is server Error.";
                exit;
            }
            $sql_query = "SELECT * FROM $quizname";
            $result = mysqli_query($conn, $sql_query);

            if (mysqli_num_rows($result) > 0) {
                // output data of each row
                while($row = mysqli_fetch_assoc($result)) {
                    $id = ($row['id']);
                    $question = ($row['question']);
                    $answer = ($row['answer']);
                    $opt1 = ($row['opt1']);
                    $opt2 = ($row['opt2']);
                    $opt3 = ($row['opt3']);
                    $opt4 = ($row['opt4']);
                    echo "
                        <tbody>
                            <tr>
                                <td>$id</td>
                                <td>$question</td>
                                <td>$answer</td>
                                <td>$opt1</td>
                                <td>$opt2</td>
                                <td>$opt3</td>
                                <td>$opt4</td>
                                <td>
                                    <form action='' method='post'>
                                        <input type='hidden' name='ids' value='$id'>
                                        <input type='hidden' name='questions' value='$question'>
                                        <input type='hidden' name='answers' value='$answer'>
                                        <input type='hidden' name='opt1s' value='$opt1'>
                                        <input type='hidden' name='opt2s' value='$opt2'>
                                        <input type='hidden' name='opt3s' value='$opt3'>
                                        <input type='hidden' name='opt4s' value='$opt4'>
                                        <input class='edit_btn' type='submit' value='Edit'>
                                        </form>
                                        <form class='edit_form' action='' method='post'>
                                            <input type='hidden' name='dropid' value='$id'>
                                            <input class='delete_btn' type='submit' value='Delete'>
                                        </form>
                                </td>
                            </tr>
                        </tbody>
                        <tbody>";
                }
            } else {
                echo "
                        <tbody>
                            <tr>
                                <td>0</td>
                                <td>You Should Create Quiz Questions!</td>
                                <td>Create Now</td>
                                <td>Please Create</td>
                                <td>This is the time</td>
                                <td>How lonely it is looking</td>
                                <td>Go Buddy</td>
                                <td>Don't Click</td>
                            </tr>
                        </tbody>";
            }


            echo "
                    </table>
                </div>
            </div>
        </div>";
        }
        close_db($conn);
        ?>

    </div>
    <script>
    const answer = document.getElementById("input_answer");
    const option1 = document.getElementById("input_option1");

    answer.onkeyup = function() {
        option1.value = answer.value;
    }
    </script>
</body>

</html>