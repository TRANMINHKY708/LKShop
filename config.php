
<?php
session_start();

// initializing variables
$username = "";
$email    = "";
$errors = array(); 

// connect to the database
define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'PuneethReddy');
   define('DB_PASSWORD', '');
   define('DB_DATABASE', 'ecommerece');
   $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($username)) { array_push($errors, "Hãy nhập tên"); }
  if (empty($email)) { array_push($errors, "Hãy nhập Email"); }
  if (empty($password_1)) { array_push($errors, "Hãy nhập mật khẩu"); }
  if ($password_1 != $password_2) {
	array_push($errors, "Xác thực mật khẩu không chính xác");
  }

  // first check the database to make sure 
  // a user does not already exist with the same username and/or email
  $user_check_query = "SELECT * FROM register WHERE Name='$username' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  
  if ($user) { // if user exists
    if ($user['Name'] === $username) {
      array_push($errors, "Tên đăng nhập đã tồn tại");
    }

    if ($user['email'] === $email) {
      array_push($errors, "Email đã tồn tại");
    }
  }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
  	$password = md5($password_1);//encrypt the password before saving in the database

  	$query = "INSERT INTO register (Name, email, password) 
  			  VALUES('$username', '$email', '$password')";
  	mysqli_query($db, $query);
  	$_SESSION['Name'] = $username;
  	$_SESSION['success'] = "Bạn đã đăng nhập";
  	header('location: index.php');
  }
}
if (isset($_POST['login_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['email']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($username)) {
  	array_push($errors, "Hãy nhập Email");
  }
  if (empty($password)) {
  	array_push($errors, "Hãy nhập mật khẩu");
  }

  if (count($errors) == 0) {
  	$password = md5($password);
  	$query = "SELECT * FROM register WHERE email='$username' AND password='$password'";
  	$results = mysqli_query($db, $query);
  	if (mysqli_num_rows($results) == 1) {
  	  $_SESSION['email'] = $username;
  	  $_SESSION['success'] = "Bạn đã đăng nhập";
  	  header('location: index.php');
  	}else {
  		array_push($errors, "Sai tài khoản hoặc mật khẩu");
  	}
  }
}

?>