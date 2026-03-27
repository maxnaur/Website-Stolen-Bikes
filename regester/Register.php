<?php
if (submit_button("","","") == "") {
  
}

function createUser(){
  $userFirstName = $_POST["firstName"];
  $userLastName = $_POST["lastName"];

  $userEmail1 = $_POST["email"];
  $userEmail2 = $_POST["confirmEmail"];
  $userPhone = $_POST["phone"];

  $userBirth = $_POST["DOB"];

  $userAddressLine1 = $_POST["addressLine1"];
  $userAddressLine2 = $_POST["addressLine2"];
  $userPostcode = $_POST["postcode"];
  $userCounty = $_POST["county"];
  $userCity = $_POST["city"];

  $userPassword1 = $_POST["password"];
  $userPassword2 = $_POST["confirmPassword"];
  

  echo "Thanks, " . $userFirstName ." ". $userLastName . " for signing up";
  echo " with email ". $userEmail;

  $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);
  $saltPassword = password_hash($userPassword, PASSWORD_DEFAULT, [""=> $saltPassword]);
  echo "hashed password ". $hashedPassword ."";
  echo "Salt password ". $saltPassword ."";
}
?>



<?php

if($_POST['phpfunction'] == 'createUser') {
  createUser();
}

function createUser() {
  
  $firstname = $_POST['firstName'];
  $lastname = $_POST['lastName'];
  $email = $_POST['email'];
  $pass = $_POST['password'];
  
  include "../include/config.php";
  
  $sql = "SELECT * FROM `tbl_user` WHERE email='$email'";
  
  $query = mysqli_query($connection, $sql);

  if(mysqli_num_rows($query) > 0){
    echo "This email already registered.";
    return;
  }
  
  $sql = "INSERT INTO `tbl_user`".
        " values ".
        "('$firstname', '$lastname', '$email', '$pass')";
  
  if(mysqli_query($connection, $sql)) {
    echo "true";
  } else {
    echo mysqli_error($connection);
    return;
  }
  
  mysqli_close($connection);
} 

?>

// Example create_register.php
<?php
require_once 'db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Validate inputs
  // ... (your validation logic)
  
  // Process registration
  $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password_hash, ...) VALUES (?, ?, ?, ?, ...)"); 
  $stmt->execute([$_POST['firstName'], $_POST['lastName'], $_POST['email'], password_hash($_POST['password'], PASSWORD_DEFAULT), ...]);
  
  // Redirect to login with success message
  header("Location: login.html?success=Account created successfully!");
  exit();
}
?>
