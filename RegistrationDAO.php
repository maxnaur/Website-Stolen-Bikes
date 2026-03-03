<?php
if($_POST['phpFunction'] == "createUser"){
    creatUser();
}

function createUser(){
    $userFistNmae = $_POST["firstName"];
    $userLastNmae = $_POST["lastName"];
    $userEmail = $_POST["email"];
    $userPassword = $_POST["password"];

    echo "Thanks, " . $userFistNmae ." ". $userLastNmae . " for sighning in";
    echo $userEmail ." email";
}