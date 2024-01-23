<?php
include 'db_conn.php';
include 'admin-actions.php';

session_start();

// Function to add an admin
function addAdmin($conn, $user_id) {
    $updateQuery = "UPDATE users SET is_admin = 1 WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

// Function to delete a user


if (isset($_SESSION['id'], $_SESSION['user_name'], $_SESSION['is_admin']) && $_SESSION['is_admin']) {
    // Query to fetch regular users
    $userQuery = "SELECT * FROM users WHERE is_admin = 0";
    $userResult = mysqli_query($conn, $userQuery);

    // Query to fetch admin users
    $adminQuery = "SELECT * FROM users WHERE is_admin = 1";
    $adminResult = mysqli_query($conn, $adminQuery);

    // Query to fetch uploaded files with specific users
    $documentQuery = "SELECT u.id, u.user_name, u.name, u.email, d.cv_path FROM users u 
                      LEFT JOIN user_documents d ON u.id = d.user_id";
    $documentResult = mysqli_query($conn, $documentQuery);
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Dashboard</title>
        <link rel="stylesheet" type="text/css" href="style.css">
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

            h1, h2 {
                color: #4CAF50;
            }

            table {
                width: 80%;
                margin: 20px auto;
                border-collapse: collapse;
            }

            table, th, td {
                border: 1px solid #4CAF50;
            }

            th, td {
                padding: 15px;
                text-align: left;
            }

            th {
                background-color: #4CAF50;
                color: white;
            }

            ul {
                list-style-type: none;
                padding: 0;
            }

            li {
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

            .document-actions {
                display: flex;
                justify-content: space-between;
                margin-bottom: 20px;
            }

            .document-actions a {
                margin-right: 10px;
            }
        </style>
    </head>

    <body>
       
            <h1>Welcome, Admin <?php echo htmlspecialchars($_SESSION['name']); ?></h1>
        

        <div class="document-actions">
            <h2>Administrators</h2>
        </div>
        <table>
            <tr>
                <th>User Name</th>
                <th>Name</th>
                <th>Email</th>
            </tr>
            <?php
            while ($adminRow = mysqli_fetch_assoc($adminResult)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($adminRow['user_name']) . "</td>";
                echo "<td>" . htmlspecialchars($adminRow['name']) . "</td>";
                echo "<td>" . htmlspecialchars($adminRow['email']) . "</td>";
                // echo "<td><button class='button' onclick='deleteUser({$adminRow['id']})'>Delete</button></td>";
                echo "</tr>";
            }
            ?>
        </table>

        <div class="document-actions">
            <h2> Users Data</h2>
        </div>
        <!-- <table>
            <tr>
                <th>User Name</th>
                <th>Name</th>
                <th>Email</th>
            </tr>
            <?php
            while ($userRow = mysqli_fetch_assoc($userResult)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($userRow['user_name']) . "</td>";
                echo "<td>" . htmlspecialchars($userRow['name']) . "</td>";
                echo "<td>" . htmlspecialchars($userRow['email']) . "</td>";
                echo "<td><button class='button' onclick='deleteUser({$userRow['id']})'>Delete</button></td>";
                echo "</tr>";
            }
            ?>
        </table> -->

        <table>
            <tr>
                <th>User Name</th>
                <th>Name</th>
                <th>Email</th>
                <th>Uploaded File</th>
                <th>Action</th>
            </tr>
            <?php
            while ($documentRow = mysqli_fetch_assoc($documentResult)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($documentRow['user_name']) . "</td>";
                echo "<td>" . htmlspecialchars($documentRow['name']) . "</td>";
                echo "<td>" . htmlspecialchars($documentRow['email']) . "</td>";
                echo "<td>{$documentRow['cv_path']}</td>";
                echo "<td><a href='#' class='button' onclick='previewDocument(\"{$documentRow['cv_path']}\")'>View</a></td>";
                echo "</tr>";
            }
            ?>
        </table>

        <a href="logout.php" class="button">Logout</a>

        <script>
            function addAdmin() {
                var user_id = prompt("Enter the user ID to make admin:");
                if (user_id !== null) {
                    // Use AJAX to send the request to add admin
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "admin-actions.php", true);
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            // Reload the page after the action is completed
                            location.reload();
                        }
                    };
                    xhr.send("action=addAdmin&user_id=" + user_id);
                }
            }

            function deleteUser(userId) {
            var confirmDelete = confirm("Are you sure you want to delete this user?");
            if (confirmDelete) {
                // Use AJAX to send the request to delete user
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "admin-actions.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        // Reload the page after the action is completed
                        location.reload();
                    }
                };
                xhr.send("action=deleteUser&user_id=" + userId); // Pass user ID as a parameter
            }
        }

            function editUser(userId) {
                // Implement edit functionality here
                alert("Edit user with ID: " + userId);
            }

            function previewDocument(cvPath) {
                window.open(cvPath, "_blank");
            }
        </script>
    </body>

    </html>

    <?php
    mysqli_close($conn); // Close the database connection
} else {
    header("Location: index.php");
    exit();
}
?>
