<?php
session_start();

// initializing variables
$username = "";
$email    = "";
$errors = array(); 

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'prplregistration');

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receiving all input values from the form
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

  //form validation, if there's an empty box, a message will pop up in the box by adding (array_push()) corresponding error unto $errors array
  if (empty($username)) { array_push($errors, "Username can not be empty!"); }
  if (empty($email)) { array_push($errors, "Email can not be empty!"); }
  if (empty($password_1)) { array_push($errors, "Password can not be empty!"); }
  if ($password_1 != $password_2) {
	array_push($errors, "Password does not match");
  }

  //checking if there is already the same user or email in the database
  $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  
  if ($user) { // if exists
    if ($user['username'] === $username) {
      array_push($errors, "Username already exists"); // display message
    }

    if ($user['email'] === $email) {
      array_push($errors, "email already exists");
    }
  }

//if there's no error, register the user
  if (count($errors) == 0) {
  	$password = md5($password_1); //password encryption, to make it more secure

  	$query = "INSERT INTO users (username, email, password) 
  			  VALUES('$username', '$email', '$password')";
  	mysqli_query($db, $query);
  	$_SESSION['username'] = $username;
  	$_SESSION['success'] = "You are now logged in";
  	header('location: index.php');
  }
}

// LOGIN USER
if (isset($_POST['login_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($username)) {
  	array_push($errors, "Username can not be empty!"); //if the username is empty, will display this pop up
  }
  if (empty($password)) {
  	array_push($errors, "Password can not be empty!"); //if the password is empty, will display this pop up
  }

  if (count($errors) == 0) {
  	$password = md5($password);
  	$query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
  	$results = mysqli_query($db, $query);
  	if (mysqli_num_rows($results) == 1) {
  	  $_SESSION['username'] = $username;
  	  $_SESSION['success'] = "You are now logged in";
  	  header('location: index.php');
  	}else {
  		array_push($errors, "Invalid password / username");
  	}
  }
}

?>
