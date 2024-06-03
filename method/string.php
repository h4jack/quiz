<?php
    function is_uname($input) { //check if username is valid or not. may only contain starts with a-z small_case and only can contain a-z, 0-9, _;
        $pattern = '/^[a-z][a-z0-9_]{3,29}$/i';

        if (preg_match($pattern, $input)) {
            return true;
        } else {
            return false;
        }
    }
?>