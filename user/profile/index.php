<?php
include "./../../db/dbconn.php";
include "./../../div/nav.php";
include "./../../submit/index.php";


session_start();
$loggedin = false;

if(isset($_SESSION['quiz_user_name'])){
    $loggedin = true;
    $username = $_SESSION['quiz_user_name'];
    $name = $_SESSION['name'];
}
if(isset($_POST['logout']) || !$loggedin){
    unset($_SESSION['quiz_user_name']);
    unset($_SESSION['name']);
    header("Location: ../login");
    exit;
}

if(isset($_POST['show_result'])){
    $_SESSION['user_quiz_result_name'] = $_POST['show_result'];
    $_SESSION['post_type'] = "user";
    header("Location: ../../result/");
}
$conn = connect_db("result");
create_result_table();
$sql_query = "SELECT * FROM result WHERE username='$username'";
$result = mysqli_query($conn, $sql_query);
close_db($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="/quiz/css/main.css">
    <link rel="stylesheet" href="/quiz/css/nav.css">
    <link rel="stylesheet" href="/quiz/css/body.css">
    <link rel="stylesheet" href="/quiz/css/button.css">
    <link rel="stylesheet" href="/quiz/css/table.css">
    <link rel="stylesheet" href="/quiz/css/input.css">
</head>
<style>
.body_left {
    width: 30%;
    padding: 10px;
    
}

form{
    width: 100%;
    height: 100%;
}

.body_right {
    width: 100%;
    height: 500px;
    padding-bottom: 0px;
}

.quizes {
    display: flex;
    flex-direction: column;
    width: 100%;
    height: 100%;
    margin: auto;
    padding: 0px;
    overflow-Y: auto;
    overflow-X: auto;

}

.q_table {
    width: 100%;
    height: 100%;
    padding: 0px;
    margin: 0px;
    text-align: center;
    margin: 0px;
    padding: 0px;

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
</style>

<body>
    <div class="main">
        <?php
        echo_nav($name, $username, "Name", "Username");
        ?>
        <div class='body'>
            <div class="body_left">
                <h3>Search Quiz</h3>
                <form action="goto.php" method="post">
                    <input class="input_txt" minlength="4" maxlength="30" type="text" name="quiz_search" required>
                    <input class="submit_btn" type="submit" value="Go">
                    <?php
                    if(isset($_SESSION['error'])){
                        echo "<p style='color:red;'>". $_SESSION['error']. "</p>";
                        unset($_SESSION['error']);
                    }
                    ?>
                </form>
            </div>
            <div class="body_right">
                <div class='quiz_heading'>
                    <h2>Given Quizes</h2>
                </div>
                <div class='quizes'>
                    <table class='q_table'>
                        <thead>
                            <tr>
                                <th>SL NO</th>
                                <th>Quiz Name</th>
                                <th>Quiz Description</th>
                                <th>Submit Time</th>
                                <th>Click</th>
                            </tr>
                        </thead>
                    <?php
                        if(mysqli_num_rows($result)){
                            $i = 1;
                            $conn = connect_db("users");
                            while($row = mysqli_fetch_assoc($result)){
                                $quizname = $row['quizname'];
                                $submit_time = $row['up_date'];
                                $submit_status = $row['is_submit'];
                                $sql_query = "SELECT * FROM quizauth 
                                            WHERE quizname='$quizname'";
                                $result_two = mysqli_query($conn, $sql_query);
                                if(mysqli_num_rows($result_two) == 1){
                                    $description = mysqli_fetch_assoc($result_two)['description'];
                                }else{
                                    unset($_SESSION['quiz_user_name']);
                                    echo "you have been logged out. <br>
                                    there is a error occured while loading your quizes. <br>
                                    if you are facing this error again. contect support. <br>
                                    <a href='../login'>login again</a>";
                                    exit;
                                }

                                echo "
                        <tbody>
                            <tr>
                                <td>$i</td>
                                <td>$quizname</td>
                                <td>";
                            if($description == ""){
                                echo "No Description Given";
                            }else{
                                echo "$description</td>";
                            }
                            if($submit_status){
                                echo "
                                <td>$submit_time</td>
                                <td>
                                    <form action='' method='post'>
                                        <input type='hidden' name='show_result' value='$quizname'>
                                        <input class='edit_btn' type='submit' value='View'>
                                    </form>
                                </td>
                                ";
                            }else{
                                echo "
                                <td>Not Submitted</td>
                                <td>
                                    <form action='goto.php' method='post'>
                                        <input type='hidden' name='quiz_search' value='$quizname'>
                                        <input class='edit_btn' style='background-color:#f43a6ef0;' type='submit' value='Go'>
                                    </form>
                                </td>";
                            }
                                echo "
                            </tr>
                        </tbody>";
                                $i++;
                            }
                            close_db($conn);
                        }else{
                            echo "
                        <tbody>
                            <tr>
                                <td>0</td>
                                <td>No Quiz</td>
                                <td>You have not given any quiz yet. try to search your quiz on the search field.</td>
                                <td>here the submition time of quiz will is shown</td>
                                <td>No Operation</td>
                            </tr>
                        </tbody>";
                        } 
                    ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>