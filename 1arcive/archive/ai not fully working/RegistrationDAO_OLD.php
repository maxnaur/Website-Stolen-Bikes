<?php
if($_POST['phpfunction'] == "createUser"){
  createUser();
}

function createUser(){
  $userFirstName = $_POST["firstName"];
  $userLastName = $_POST["lastName"];
  $userEmail = $_POST["email"];
  $userPassword = $_POST["password"];

  echo "Thanks, " . $userFirstName ." ". $userLastName . " for signing up";
  echo " with email ". $userEmail;

  $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);
  $saltPassword = password_hash($userPassword, PASSWORD_DEFAULT, [""=> $saltPassword]);
  echo "hashed password ". $hashedPassword ."";
  echo "Salt password ". $saltPassword ."";
}