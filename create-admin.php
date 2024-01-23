<?php
session_start();
include "db_conn.php";

// Ensure that the database connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// User information
$uname = 'tacy';
$pass = 'tacy123456';
$name = 'Tacy Njiru';
$email = 'tacynjiru@gmail.com';

// Hash the password
$pass = md5($pass);


// SQL query to insert admin user
$sql = "INSERT INTO users (user_name, password, name, is_admin, email) 
        VALUES ('$uname', '$pass', '$name', 1, '$email')";

// Execute the query
if (mysqli_query($conn, $sql)) {
    echo "Admin user created successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
?>
