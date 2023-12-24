<?php
include "database.php";
session_start();

// Check if the login was successful
$loginSuccessful = false;

if (isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Use prepared statements to prevent SQL injection
    $sql = "SELECT * FROM users WHERE username = :username AND password = :password";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":username", $username, PDO::PARAM_STR);
    $stmt->bindParam(":password", $password, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION["id"] = $row["id"];
        $_SESSION["username"] = $row["username"];
        $loginSuccessful = true;
        echo "<script>window.open('dashboard.php','_self');</script>";
    } else {
        echo "<div class='error'>Invalid Username or Password</div>";
    }
}

if (isset($_GET["mes"])) {
    // Display an error message (if provided in the URL)
    echo "<div class='error'>{$_GET["mes"]}</div>";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>User Login</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="register.php">Sign Up</a></li>
            </ul>
        </nav>
    </header>
    <div class="login-form">
        <h2>User Login</h2>
        <?php
        if ($loginSuccessful) {
            // Display a success message if login was successful
            echo '<p style="color: green;">Login Successful!</p>';
        }
        ?>
        <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter your username">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password">
            <input type="submit" value="Login" name="login">
        </form>
    </div>
</body>

</html>
