<?php
session_start(); // Start the session if not already started

// Check if the user is logged in. You should have your own user authentication logic here.
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page or display an error message.
    header("Location: login.php");
    exit();
}

// Include your database connection code here.
// Example: require_once("db_connect.php");

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize the user inputs
    $currentPassword = $_POST["current_password"];
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];

    // You should implement proper validation and error handling here.
    // For example, check if the current password is correct, and if the new password matches the confirmation.

    // If validation is successful, update the user's password in the database
    if ($newPassword === $confirmPassword) {
        // Hash the new password before storing it in the database.
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the user's password in the database (replace 'users' with your actual table name)
        $userId = $_SESSION['user_id'];
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("si", $hashedPassword, $userId);

        if ($stmt->execute()) {
            // Password update successful
            $successMessage = "Password updated successfully.";
        } else {
            // Password update failed
            $errorMessage = "Error updating password. Please try again later.";
        }
    } else {
        $errorMessage = "New password and confirm password do not match.";
    }
}

// Close the database connection if you opened it.

?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
</head>
<body>
    <h2>Change Password</h2>

    <?php
    if (isset($successMessage)) {
        echo '<div style="color: green;">' . $successMessage . '</div>';
    }

    if (isset($errorMessage)) {
        echo '<div style="color: red;">' . $errorMessage . '</div>';
    }
    ?>

    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <label for="current_password">Current Password:</label>
        <input type="password" name="current_password" required><br>

        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" required><br>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" required><br>

        <input type="submit" value="Change Password">
    </form>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
