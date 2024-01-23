<?php 
session_start(); 
include "db_conn.php";

if (isset($_POST['uname']) && isset($_POST['password']) && isset($_POST['admin'])) {

    function validate($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $uname = validate($_POST['uname']);
    $pass = validate($_POST['password']);
    $adminOption = $_POST['admin']; // Added admin option

    if (empty($uname)) {
        header("Location: index.php?error=User Name is required");
        exit();
    } else if(empty($pass)){
        header("Location: index.php?error=Password is required");
        exit();
    } else {

        // Hash the password
        $pass = md5($pass);

        // Check if the user is an admin
        $adminCondition = ($adminOption == 'yes') ? "AND is_admin = 1" : "";

        $sql = "SELECT * FROM users WHERE user_name='$uname' AND password='$pass' $adminCondition";

        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            
            if ($row['user_name'] === $uname && $row['password'] === $pass) {
                $_SESSION['user_name'] = $row['user_name'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['id'] = $row['id'];
                $_SESSION['is_admin'] = $row['is_admin']; // Assuming 'is_admin' is a column in your 'users' table

                if ($_SESSION['is_admin']) {
                    header("Location: admin_dashboard.php"); // Redirect to admin dashboard
                    exit();
                } else {
                    header("Location: home.php"); // Redirect to regular user dashboard
                    exit();
                }
            } else {
                header("Location: index.php?error=Incorrect User name or password");
                exit();
            }
        } else {
            header("Location: index.php?error=Incorrect User name or password or not an admin");
            exit();
        }
    }
    
} else {
    header("Location: index.php");
    exit();
}
?>
