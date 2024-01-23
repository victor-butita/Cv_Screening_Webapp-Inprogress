<!-- view_document.php -->
<?php
session_start();

if (isset($_SESSION['id'], $_SESSION['user_name'], $_SESSION['is_admin']) && $_SESSION['is_admin']) {
    include 'db_conn.php';

    if (isset($_GET['user_id'])) {
        $userId = mysqli_real_escape_string($conn, $_GET['user_id']);

        $documentQuery = "SELECT u.user_name, u.name, d.cv_path FROM users u 
                          LEFT JOIN user_documents d ON u.id = d.user_id
                          WHERE u.id = $userId";
        $documentResult = mysqli_query($conn, $documentQuery);

        if ($documentRow = mysqli_fetch_assoc($documentResult)) {
            $userName = htmlspecialchars($documentRow['user_name']);
            $name = htmlspecialchars($documentRow['name']);
            $cvPath = htmlspecialchars($documentRow['cv_path']);
        } else {
            // Handle user not found
            header("Location: admin_dashboard.php");
            exit();
        }
    } else {
        // Redirect to admin dashboard if user_id is not set
        header("Location: admin_dashboard.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Document</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 20px;
        }

        div {
            max-width: 600px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        p {
            margin-bottom: 10px;
        }

        a.button {
            text-decoration: none;
            color: #fff;
            background-color: #4CAF50;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
        }

        a.button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <header>
        <h1>View Document - <?php echo $userName; ?></h1>
    </header>

    <div>
        <p>User Name: <?php echo $userName; ?></p>
        <p>Name: <?php echo $name; ?></p>
        <p>Uploaded File: <a href="<?php echo $cvPath; ?>" target="_blank">View Document</a></p>
    </div>

    <a href="admin_dashboard.php" class="button">Back to Admin Dashboard</a>

    <?php
    mysqli_close($conn); // Close the database connection
    ?>
</body>

</html>
