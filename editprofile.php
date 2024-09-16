<?php
session_start();
include('config.php');

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Fetch current user data
$stmt = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();

// Fetch all available accessibility issues
$issues_stmt = $conn->prepare("SELECT issue_id, issue_name FROM accessibility_issues");
$issues_stmt->execute();
$all_issues_result = $issues_stmt->get_result();
$issues_stmt->close();

// Fetch user's current accessibility issues
$selected_issues_stmt = $conn->prepare("
    SELECT issue_id 
    FROM user_accessibility 
    WHERE user_id = ?
");
$selected_issues_stmt->bind_param("i", $user_id);
$selected_issues_stmt->execute();
$selected_issues_result = $selected_issues_stmt->get_result();
$selected_issues = [];
while ($row = $selected_issues_result->fetch_assoc()) {
    $selected_issues[] = $row['issue_id'];
}
$selected_issues_stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($type == 'username') {
        $new_username = trim($_POST['username']);
        
        // Check if username is taken
        $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? AND user_id != ?");
        $check_stmt->bind_param("si", $new_username, $user_id);
        $check_stmt->execute();
        $check_stmt->store_result(); // Ensure results are stored
        if ($check_stmt->num_rows > 0) {
            $error = "Username is already taken.";
        } else {
            $check_stmt->close(); // Close the check statement before updating
            
            $update_stmt = $conn->prepare("UPDATE users SET username = ? WHERE user_id = ?");
            $update_stmt->bind_param("si", $new_username, $user_id);
            $update_stmt->execute();
            $update_stmt->close(); // Close the update statement
            
            header("Location: dashboard.php");
            exit;
        }
        $check_stmt->close(); // Close after use

    } elseif ($type == 'email') {
        $new_email = trim($_POST['email']);
        
        // Check if email is taken
        $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $check_stmt->bind_param("si", $new_email, $user_id);
        $check_stmt->execute();
        $check_stmt->store_result(); // Ensure results are stored
        if ($check_stmt->num_rows > 0) {
            $error = "Email is already taken.";
        } else {
            $check_stmt->close(); // Close the check statement before updating

            $update_stmt = $conn->prepare("UPDATE users SET email = ? WHERE user_id = ?");
            $update_stmt->bind_param("si", $new_email, $user_id);
            $update_stmt->execute();
            $update_stmt->close(); // Close the update statement
            
            header("Location: dashboard.php");
            exit;
        }
        $check_stmt->close(); // Close after use

    } elseif ($type == 'issues') {
        $new_issues = isset($_POST['issues']) ? $_POST['issues'] : [];
        
        // Clear existing issues
        $clear_stmt = $conn->prepare("DELETE FROM user_accessibility WHERE user_id = ?");
        $clear_stmt->bind_param("i", $user_id);
        $clear_stmt->execute();
        $clear_stmt->close(); // Close after clearing

        // Insert new issues
        $insert_stmt = $conn->prepare("INSERT INTO user_accessibility (user_id, issue_id) VALUES (?, ?)");
        foreach ($new_issues as $issue_id) {
            $insert_stmt->bind_param("ii", $user_id, $issue_id);
            $insert_stmt->execute();
        }
        $insert_stmt->close(); // Close after inserting issues
        
        header("Location: dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        // JavaScript function to display confirmation dialog
        function confirmChange() {
            return confirm("Are you sure you want to update your account details?");
        }
    </script>
</head>
<body>

<?php include('header.php'); ?>

<div class="container">
    <h2>Edit Your Profile</h2>

    <?php if (isset($error)): ?>
        <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <div class="form-container">
        <?php if ($type == 'username'): ?>
            <form method="POST" action="editprofile.php?type=username" onsubmit="return confirmChange();">
                <label for="username">New Username:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                <button type="submit" class="button">Save Changes</button>
            </form>

        <?php elseif ($type == 'email'): ?>
            <form method="POST" action="editprofile.php?type=email" onsubmit="return confirmChange();">
                <label for="email">New Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                <button type="submit" class="button">Save Changes</button>
            </form>

        <?php elseif ($type == 'issues'): ?>
            <form method="POST" action="editprofile.php?type=issues" onsubmit="return confirmChange();">
                <label for="issues">Select Your Accessibility Issues:</label><br>
                <?php while ($row = $all_issues_result->fetch_assoc()): ?>
                    <input type="checkbox" name="issues[]" value="<?php echo $row['issue_id']; ?>" 
                    <?php echo in_array($row['issue_id'], $selected_issues) ? 'checked' : ''; ?>>
                    <?php echo htmlspecialchars($row['issue_name']); ?><br>
                <?php endwhile; ?>
                <button type="submit" class="button">Save Changes</button>
            </form>
        <?php else: ?>
            <p class="error-message">Invalid option.</p>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p>&copy; 2024 Your Website. All rights reserved.</p>
</footer>

</body>
</html>
