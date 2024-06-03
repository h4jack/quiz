<?php
function create_result_table() {
    $conn2 = connect_db("result");
    if(!table_exists("result", $conn2)) {
        $sql_query = "CREATE TABLE IF NOT EXISTS result(
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(30) NOT NULL, 
            quizname VARCHAR(30) NOT NULL, 
            is_submit TINYINT(1) DEFAULT 0,
            reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            up_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        if(!mysqli_query($conn2, $sql_query)) {
            echo "Error on Server Side.";
            exit;
        }
    }
    close_db($conn2);
}

function row_exists($username, $user_quiz_name) {
    $conn2 = connect_db("result");
    $query = "SELECT EXISTS(SELECT * FROM result WHERE username = '$username' AND quizname = '$user_quiz_name')";
    $result = mysqli_fetch_assoc($conn2->query($query));
    close_db($conn2);
    return current($result) == 1;
}

function set_false() {
    if(isset($_SESSION['quiz_user_name']) && isset($_SESSION['user_quiz_name'])) {
        $username = $_SESSION['quiz_user_name'];
        $user_quiz_name = $_SESSION['user_quiz_name'];
        if (!row_exists($username, $user_quiz_name)) {
            $conn2 = connect_db("result");
            $sql_query = "INSERT INTO result (username, quizname, is_submit) VALUES ('$username', '$user_quiz_name', 0)";
            if (!mysqli_query($conn2, $sql_query)) {
                echo "Error on Server Side.";
                exit;
            }
        } else {
            $sql_query = "UPDATE result SET is_submit=0 WHERE quizname='$user_quiz_name' AND username='$username'";
            if (!mysqli_query($conn2, $sql_query)) {
                echo "Error on Server Side.";
                exit;
            }
        }
        close_db($conn2);
    }
}

function set_true() {
    $conn2 = connect_db("result");
    if(isset($_SESSION['quiz_user_name']) && isset($_SESSION['user_quiz_name'])) {
        $username = $_SESSION['quiz_user_name'];
        $user_quiz_name = $_SESSION['user_quiz_name'];
        if (!row_exists($username, $user_quiz_name)) {
            $sql_query = "INSERT INTO result (username, quizname, is_submit) VALUES ('$username', '$user_quiz_name', 1)";
            if (!mysqli_query($conn2, $sql_query)) {
                echo "Error on Server Side.";
                exit;
            }
        } else {
            $sql_query = "UPDATE result SET is_submit=1 WHERE quizname='$user_quiz_name' AND username='$username'";
            if (!mysqli_query($conn2, $sql_query)) {
                echo "Error on Server Side.";
                exit;
            }
        }
    }
    close_db($conn2);
}

function is_submit() {
    $conn2 = connect_db("result");
    if(isset($_SESSION['quiz_user_name']) && isset($_SESSION['user_quiz_name'])) {
        $username = $_SESSION['quiz_user_name'];
        $user_quiz_name = $_SESSION['user_quiz_name'];
        $sql_query = "SELECT is_submit FROM result WHERE quizname='$user_quiz_name' AND username='$username'";
        $result = mysqli_query($conn2, $sql_query);
        close_db($conn2);
        if(mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row["is_submit"];
        } else {
            return false;
        }
    }
}

?>