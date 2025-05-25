<?php
include 'db_connect.php'; // Include the database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect value of input field
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $password = $_POST['password'];
    // $confirm_password = $_POST['confirm_password']; // Confirm password check should be done client-side or before this point

    // Basic validation (you would do more thorough validation in a real application)
    if (!empty($first_name) && !empty($last_name) && !empty($username) && !empty($email) && !empty($gender) && !empty($password)) {
        // Prepare an insert statement
        $sql = "INSERT INTO users (first_name, last_name, username, email, gender, password) VALUES (?, ?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssssss", $first_name, $last_name, $username, $email, $gender, $password);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                echo "<p>New record created successfully</p>";
            } else {
                echo "<p>Error: " . $stmt->error . "</p>";
            }

            // Close statement
            $stmt->close();
        } else {
             echo "<p>Error preparing statement: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>Please fill in all required fields.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <h2>Making Every Moment Count — Safely.</h2>
            <p>Create and account to Join Our Community</p>
        </div>
        <div class="right-panel">
            <h2>Sign up</h2>
            <p>Join the community today!</p>
            <button class="google-signin-button"><img src="https://img.icons8.com/color/48/000000/google-logo.png" alt="Google" width="30"> Sign up with Google</button>
            <div class="options">or</div>
            <form action="signup.php" method="post">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" placeholder="John">
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" placeholder="Doe">
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="johndoe">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="uiuxsaeed@gmail.com">
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="e.g. 2McDc6cdN8jk9z">
                </div>
                <button type="submit">Sign up</button>
            </form>
             <div class="create-account">
                    Already Have an account? <a href="login.php">Login</a>
                </div>
        </div>
    </div>
</body>
</html> 