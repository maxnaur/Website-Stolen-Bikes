<?php
// handle session cookie
if(isset($_POST['phpFunction'])) {
  if($_POST['phpFunction'] == 'checkLogin') {
    checkLogin();
  } elseif($_POST['phpFunction'] == 'logout') {
    logout();
  }
}

function logout () {
  session_start();
  
  //Destroying all sessions
  if( session_destroy() )
  {
  echo 'true';
  } else {
  echo 'false';
  //header( "Location: ../login/Login.html" ); 
  }
  //Redirecting to home page
  //header( "Location: ../login/Login.html" ); 
}

function checkLogin() {
  session_start();
  $uName = $_SESSION['uName'];
  if( isset( $uName ) ) {
  echo 'true';
  } else {
  echo 'false';
  }
}
?>