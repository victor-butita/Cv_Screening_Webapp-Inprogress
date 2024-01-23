<?php
session_start();
include "db_conn.php";

// Ensure that the database connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['uname'], $_POST['password'], $_POST['email'], $_POST['name'], $_POST['re_password'])) {

    $uname = $conn->real_escape_string($_POST['uname']);
    $pass = $conn->real_escape_string($_POST['password']);
    $email = $conn->real_escape_string($_POST['email']);
    $re_pass = $conn->real_escape_string($_POST['re_password']);
    $name = $conn->real_escape_string($_POST['name']);

    $user_data = 'uname=' . $uname . '&name=' . $name . '&email=' . $email;

    if (empty($uname) || empty($pass) || empty($re_pass) || empty($name) || empty($email)) {
        header("Location: signup.php?error=All fields are required&$user_data");
        exit();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: signup.php?error=Invalid email format&$user_data");
        exit();
    } elseif ($pass !== $re_pass) {
        header("Location: signup.php?error=The confirmation password does not match&$user_data");
        exit();
    } elseif (strlen($pass) < 8) {
        header("Location: signup.php?error=Password should be at least 8 characters long&$user_data");
        exit();
    } else {
        // using prepared statements to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_name=?");
        $stmt->bind_param("s", $uname);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            header("Location: signup.php?error=The username is taken, try another&$user_data");
            exit();
        } else {
            // hashing the password
            $pass = md5($pass);

            $sql = "SELECT * FROM users WHERE user_name='$uname' ";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                header("Location: signup.php?error=The username is taken, try another&$user_data");
                exit();
            } else {
                $sql2 = "INSERT INTO users(user_name, password, name,email) VALUES('$uname', '$pass', '$name','$email')";
                $result2 = mysqli_query($conn, $sql2);
                if ($result2) {
                    header("Location: signup.php?success=Your account has been created successfully");
                    exit();
                } else {
                    header("Location: signup.php?error=unknown error occurred&$user_data");
                    exit();
                }
            }
        }
    }
} else {
    header("Location: signup.php");
    exit();
}
?>
