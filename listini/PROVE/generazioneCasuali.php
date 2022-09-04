<?php

function generateRandomString($length = 30) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}



for ($i=1;$i<100;$i++)
    echo  generateRandomString().'<br>';  // OR: generateRandomString(24)

?>
