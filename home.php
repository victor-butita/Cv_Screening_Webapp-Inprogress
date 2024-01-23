<?php 
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {

    // Include your database connection file
    include 'db_conn.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['cv'])) {
        // Handle file upload here
        $targetDirectory = "uploads/"; // Create a directory named "uploads" to store the files
        $targetFile = $targetDirectory . basename($_FILES["cv"]["name"]);
        
        if (move_uploaded_file($_FILES["cv"]["tmp_name"], $targetFile)) {
            // File upload successful, now insert into the database
            $userId = $_SESSION['id'];
            $cvPath = $targetFile;

            // Insert into the user_documents table
            $insertQuery = "INSERT INTO user_documents (user_id, cv_path) VALUES ($userId, '$cvPath')";
            $result = mysqli_query($conn, $insertQuery);

            if ($result) {
                echo "The file " . htmlspecialchars(basename($_FILES["cv"]["name"])) . " has been uploaded and recorded in the database.";

                // Display a list of uploaded documents for the current user
                $userDocumentsQuery = "SELECT cv_path FROM user_documents WHERE user_id = $userId";
                $userDocumentsResult = mysqli_query($conn, $userDocumentsQuery);

                if (mysqli_num_rows($userDocumentsResult) > 0) {
                    echo "<h2>Your Uploaded Documents:</h2>";
                    echo "<ul>";
                    while ($documentRow = mysqli_fetch_assoc($userDocumentsResult)) {
                        echo "<li>{$documentRow['cv_path']}</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No documents uploaded yet.</p>";
                }
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
 ?>

<!DOCTYPE html>
<html>
<head>
    <title>HOME</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
     <h1>Hello, <?php echo $_SESSION['name']; ?></h1>
     
     <form action="" method="post" enctype="multipart/form-data">
        <label for="cv">Upload CV For Screening:</label>
        <input type="file" name="cv" id="cv">
        <input type="submit" value="Upload">
     </form>
     
     <a href="logout.php">Logout</a>
</body>
</html>

<?php 
} else {
    header("Location: index.php");
    exit();
}
?>
