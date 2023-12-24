<?php
include "database.php";
session_start();

// Check if the registration was successful
$registrationSuccessful = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if (empty($username) || empty($password) || empty($email)) {
        $_SESSION['error'] = 'Please enter a valid username, email, and password';
        header('Location: index.php');
        exit;
    }

    try {
        // Create a PDO connection
        $conn = new PDO("mysql:host=localhost;dbname=expensetracker", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if the username or email already exists
        $query = "SELECT * FROM users WHERE username = :username OR email = :email";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = 'Username or email already taken';
            header('Location: register.php');
            exit;
        }

        // Hash the password before storing it in the database
        //$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert the new user
        $query = "INSERT INTO users (username, password, email) VALUES (:username, :password, :email)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $registrationSuccessful = true;
        } else {
            $_SESSION['error'] = 'Registration failed. Please try again later.';
            header('Location: register.php');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        header('Location: register.php');
        exit;
    }
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>Registration Form</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="index.php">Sign In</a></li>
            </ul>
        </nav>
    </header>
    <div class="login-form">
        <h2>Registration Form</h2>
        <?php
        // Display error message if set
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }

        // Display registration success message if registration was successful
        if ($registrationSuccessful) {
            echo '<p style="color: green;">Registration Successful!</p>';
        }
        ?>
        <form method="POST" autocomplete="off">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter your username">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password">

            <input type="submit" value="REGISTER">
        </form>
    </div>
</body>

</html>
