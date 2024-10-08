<?php
include('config.php');

$error = ""; // Initialize an empty error message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role']; // Get the role from the form (developer or general_user)
    $accessibility_issues = isset($_POST['accessibility_issues']) ? $_POST['accessibility_issues'] : []; // Array of selected issue IDs

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match. Please try again.";
    } else {
        $password = password_hash($password, PASSWORD_BCRYPT); // Hash the password

        // Check if the username already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username already exists. Please choose a different one.";
        } else {
            // Check if the email already exists
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = "Email already exists. Please use a different email.";
            } else {
                // Insert user data into users table
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $username, $email, $password, $role);
                if ($stmt->execute()) {
                    $user_id = $stmt->insert_id;

                    // Insert user accessibility issues into user_accessibility table
                    foreach ($accessibility_issues as $issue_id) {
                        $stmt = $conn->prepare("INSERT INTO user_accessibility (user_id, issue_id) VALUES (?, ?)");
                        $stmt->bind_param("ii", $user_id, $issue_id);
                        $stmt->execute();
                    }

                    // Redirect user to the login page or dashboard
                    header("Location: login.php");
                    exit;
                } else {
                    $error = "Error: Could not register user.";
                }
            }
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .register-form {
            max-width: 400px; /* Limit the width of the form */
            margin: 0 auto; /* Center the form */
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .register-form input[type="text"],
        .register-form input[type="email"],
        .register-form input[type="password"],
        .register-form select {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .register-form input[type="submit"] {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #00539CFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        .register-form input[type="submit"]:hover {
            background-color: #003d80;
        }

        .accessibility-button {
            display: inline-block;
            padding: 10px 15px;
            margin: 5px;
            background-color: #f0f0f0;
            color: #333;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            user-select: none;
        }

        .accessibility-button.selected {
            background-color: #00539CFF;
            color: white;
            border-color: #00539CFF;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
        }
    </style>
    <script>
        function toggleSelection(button, issueId) {
            const input = document.getElementById('issue-' + issueId);
            if (button.classList.contains('selected')) {
                button.classList.remove('selected');
                input.checked = false;
            } else {
                button.classList.add('selected');
                input.checked = true;
            }
        }

        // JavaScript to validate the password match
        function validateForm() {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm_password").value;

            if (password !== confirmPassword) {
                alert("Passwords do not match. Please try again.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>

<?php include('header.php'); ?>

<div class="container">
    <div class="register-form">
        <h2>Register</h2>

        <!-- Display the error message if registration fails -->
        <?php if ($error): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST" action="register.php" onsubmit="return validateForm();">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" id="password" placeholder="Password" required>
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>

            <!-- Add role selection (Developer or General User) -->
            <label for="role">Register as:</label>
            <select name="role" id="role" required>
                <option value="developer">Developer</option>
                <option value="general_user">General User</option>
            </select>

            <label for="accessibility_issues">Select your accessibility interests:</label>
            <div>
                <?php
                $result = $conn->query("SELECT issue_id, issue_name FROM accessibility_issues");
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='accessibility-button' onclick='toggleSelection(this, " . $row['issue_id'] . ")'>" . htmlspecialchars($row['issue_name']) . "</div>";
                    echo "<input type='checkbox' name='accessibility_issues[]' id='issue-" . $row['issue_id'] . "' value='" . $row['issue_id'] . "' style='display:none;'>";
                }
                ?>
            </div>

            <input type="submit" value="Register" class="button">
        </form>
        <p>Already have an account? <a href="login.php">Login Here.</a></p>
    </div>
</div>

<footer>
    <p>&copy; 2024 Digital Accessibility Project. All rights reserved.</p>
</footer>

</body>
</html>
