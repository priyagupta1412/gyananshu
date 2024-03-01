<?php
session_start();

// Initialize variables
$errors = array(); 

// Connect to the database
$db = mysqli_connect('localhost', 'root', '', 'gyananshu');

// REGISTER FACULTY
if (isset($_POST['reg_user'])) {
  // Receive all input values from the form
  $title = mysqli_real_escape_string($db, $_POST['title']);
  $firstName = mysqli_real_escape_string($db, $_POST['firstName']);
  $lastName = mysqli_real_escape_string($db, $_POST['lastName']);
  $dob = mysqli_real_escape_string($db, $_POST['dob']);
  $gender = mysqli_real_escape_string($db, $_POST['gender']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $department = mysqli_real_escape_string($db, $_POST['department']);
  $designation = mysqli_real_escape_string($db, $_POST['designation']);
  $yearOfJoining = mysqli_real_escape_string($db, $_POST['yearJoining']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password']);
  $password_2 = mysqli_real_escape_string($db, $_POST['confirmPassword']);

  // Form validation
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($password_1)) { array_push($errors, "Password is required"); }
  if ($password_1 != $password_2) {
    array_push($errors, "The two passwords do not match");
  }

  // Check the database to make sure a faculty member does not already exist with the same email
  $faculty_check_query = "SELECT * FROM faculty WHERE faculty_email='$email' LIMIT 1";
  $result = mysqli_query($db, $faculty_check_query);
  $faculty = mysqli_fetch_assoc($result);
  
  if ($faculty) {
    if ($faculty['faculty_email'] === $email) {
      array_push($errors, "Email already exists");
    }
  }

  // Register faculty if there are no errors in the form
  if (count($errors) == 0) {
    //$password = md5($password_1); // Encrypt the password before saving in the database
    $password = password_hash($password_1, PASSWORD_DEFAULT);

    $query = "INSERT INTO faculty (password_faculty, department_id, title, first_name, last_name, date_of_birth, gender, faculty_email, designation,  year_of_join) 
          VALUES('$password', '$department', '$title', '$firstName', '$lastName', '$dob', '$gender', '$email', '$designation', '$yearOfJoining')";
    mysqli_query($db, $query);
    $_SESSION['username'] = $firstName; // Adjust according to what you want to use as the session username
    $_SESSION['success'] = "You are now registered";
    header('location: login.php'); // Adjust if necessary to redirect to a different page
  }
}
if (isset($_POST['login_user'])) {
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($email)) {
    array_push($errors, "Email is required");
  }
  if (empty($password)) {
    array_push($errors, "Password is required");
  }

  if (count($errors) == 0) {
    $query = "SELECT * FROM faculty WHERE faculty_email='$email' LIMIT 1";
    $results = mysqli_query($db, $query);
    if ($row = mysqli_fetch_assoc($results)) {
      if (password_verify($password, $row['password_faculty'])) {
        // Password matches, set the session
        $_SESSION['username'] = $row['first_name']; // Adjust according to your needs
        $_SESSION['success'] = "You are now logged in";
        header('location: index.html'); // Adjust if necessary to redirect to a different page
      } else {
        // Password does not match
        array_push($errors, "email/password combination");
      }
    } else {
      // No user found
      array_push($errors, "No user found with that email");
    }
  }
}

?>
