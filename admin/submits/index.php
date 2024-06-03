<?php
include "./../../db/dbconn.php";
include "./../../div/nav.php";
include "./../../submit/index.php";

session_start();
$quizname = "";
$description = "";
$loggedin = false;

if(isset($_SESSION['quizname'])){
    $quizname = $_SESSION['quizname'];
    $description = $_SESSION['description'];
    $loggedin = true;
}

if(isset($_POST['show_result'])){
    $_SESSION['quiz_user_result_name'] = $_POST['show_result'];
    $_SESSION['post_type'] = "admin";
    header("Location: ../../result/");
}

if(isset($_POST['logout']) || !$loggedin){
    unset($_SESSION['quizname']);
    unset($_SESSION['description']);
    header("Location: ../login/");
    echo "Logged Out.";
    exit;
}
create_result_table();
$conn = connect_db("result");
$sql_query = "SELECT * FROM result WHERE quizname='$quizname'";
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
    display: none;
}

form {
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
        <?php echo_nav($quizname, $description, "Quizname", "Description", "../");?>
        <div class='body'>
            <div class="body_right">
                <div class='quiz_heading'>
                    <h2>Submit Quizes</h2>
                </div>
                <div class='quizes'>
                    <table class='q_table'>
                        <thead>
                            <tr>
                                <th>SL NO</th>
                                <th>Full Name</th>
                                <th>Username</th>
                                <th>Submition Time</th>
                                <th>Click</th>
                            </tr>
                        </thead>
                    <?php
                        if(mysqli_num_rows($result)){
                            $i = 1;
                            while($row = mysqli_fetch_assoc($result)){
                                $username = $row['username'];
                                $submit_time = $row['up_date'];
                                $submit_status = $row['is_submit'];
                                $conn = connect_db('users');
                                $sql_query = "SELECT name FROM quizuser WHERE username='$username'";
                                $result_two = mysqli_query($conn, $sql_query);
                                close_db($conn);
                                if(mysqli_num_rows($result_two) == 1){
                                    $name = mysqli_fetch_assoc($result_two)['name'];
                                }else{
                                    echo "critical error occured. someone messed with the database.";
                                    exit;
                                }
                                if(trim($name) == ""){
                                    $name = "Nobody(&#128128;)";
                                }
                                echo "
                        <tbody>
                            <tr>
                                <td>$i</td>
                                <td>$name</td>
                                <td>$username</td>";
                                if($submit_status){
                                    echo "
                                <td>$submit_time</td>";
                                }else{
                                    echo "
                                    <td>Not Submitted Yet</td>";
                                }
                                echo "
                                <td>
                                    <form action='' method='post'>
                                        <input type='hidden' name='show_result' value='$username'>
                                        <input class='edit_btn' type='submit' value='View'>
                                    </form>
                                </td>
                            </tr>
                        </tbody>";
                                $i++;
                            }
                        }else{
                            echo "
                        <tbody>
                            <tr>
                                <td>0</td>
                                <td>Not a Single Person</td>
                                <td>Not a Single Person. not Even attempted to your quiz!</td>
                                <td>Never Submitted</td>
                                <td>No Click</td>
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