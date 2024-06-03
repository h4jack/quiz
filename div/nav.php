<?php
function echo_nav($quizname = "", $description = "", $h2 = "Quiz Name", $p = "Description", $url = ""){
    echo "        <div class='nav'>
    <a style='text-decoration:none; color: #F5E8C7;' href='$url'><div class='username'>";
    if(trim($quizname) == ""){
        echo "
        <h2>$h2 : $description</h2>";
    }elseif(trim($description) == ""){
        echo "<h2>$h2 : $quizname</h2>";
    }else{
        echo"
        <h2>$h2 : $quizname</h2>
        <p>$p : $description</p>";
    }
    echo "
    </div></a>
    <div class='logout'>
        <form action='' method='post'>
            <input type='hidden' name='logout' value='logout'>
            <input type='submit' value='Log Out'>
        </form>
    </div>
</div>";
}
?>