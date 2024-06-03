<?php
if(isset($_POST['goto'])){
    $goto = $_POST['goto'];
    if($goto == 'admin' or $goto == 'user'){
        header("Location: $goto");
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
    <link rel="stylesheet" href="css/button.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #232D3F;
            transition: background-color 0.5s ease;
            color: beige;
        }

        body:hover{
            background-color: #092635;
        }

        .main {
            width: 40%;
            padding: 5%;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #092635;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            transition: transform 0.5s ease;
        }

        .main:hover {
            transform: translateY(-10px);
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
            background-color: #192635;
        }

        .submit_btn{
            width: 80%;
            text-decoration: none;
        }
        form{
            height: 100%;
            width: 100%;
            margin: auto;
            padding: auto;
        }

        @media (max-width: 768px) {
            .main {
                width: 80%;
            }
        }
    </style>
</head>
<body>
    <div class="main">
        <h1>Quiz</h1>
        <form action="" method="post">
            <input type="hidden" name="goto" value="admin">
            <input type="submit" class="submit_btn" value="Create Quiz">
        </form>
        <form action="" method="post">
            <input type="hidden" name="goto" value="user">
            <input type="submit" class="submit_btn" value="Take Quiz">
        </form>
    </div>
</body>
</html>