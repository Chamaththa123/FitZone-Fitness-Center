<?php
session_start();  // Start the session to access session variables
require 'src/config/config.php';  // Database connection

// Handle user login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Fetch user from the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'];

                // Redirect to the same page to refresh and hide login/signup buttons
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $error_message = "Invalid password!";
            }
        } else {
            $error_message = "No user found with this email!";
        }
    }

    // Handle user registration
    if (isset($_POST['register'])) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $address = $_POST['address'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);  // Hash the password

        // Check if the user already exists
        $checkEmail = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $result = $checkEmail->get_result();

        if ($result->num_rows > 0) {
            $error_message = "User already exists!";
        } else {
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, address, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $first_name, $last_name, $email, $address, $password);

            if ($stmt->execute()) {
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $first_name;

                // Redirect to the same page after successful registration
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $error_message = "Error during registration: " . $stmt->error;
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="public/assets/css/header.css">

<body>
    <div class="topnav">
        <img src="public/assets/images/logo.jpg" class='logo' style='width:80px' alt="Logo">
        <div class="split">
            <a class="active" href="#home">Home</a>
            <a class="active" href="#membership">Membership</a>
            <a href="#contact">Contact Us</a>
            <a href="#blog">Blog</a>

            <?php if (isset($_SESSION['user_name'])): ?>
            <a class="user-email" style='margin-left:20px'> <?php echo htmlspecialchars($_SESSION['user_name']); ?></a>
            <form method="post" style="display:inline;">
                <button type="submit" name="logout" class="btn">Logout</button>
            </form>

            <?php else: ?>
            <button class="btn" onclick="document.getElementById('login').style.display='block'">SIGN IN</button>
            <button class="btn" onclick="document.getElementById('register').style.display='block'">SIGN UP</button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Display error message, if any -->
    <?php if (isset($error_message)): ?>
    <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <!-- Login Modal -->
    <div id="login" class="modal">
        <form class="modal-content animate" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="imgcontainer">
                <span onclick="document.getElementById('login').style.display='none'" class="close"
                    title="Close Modal">&times;</span>
            </div>

            <div class="container">
                <label for="email"><b>Email</b></label>
                <input type="text" placeholder="Enter Email" name="email" required>

                <label for="password"><b>Password</b></label>
                <input type="password" placeholder="Enter Password" name="password" required>

                <button type="submit" name="login">Login</button>
                <label>
                    <input type="checkbox" checked="checked" name="remember"> Remember me
                </label>
            </div>
        </form>
    </div>

    <!-- Register Modal -->
    <div id="register" class="modal">
        <form class="modal-content animate" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="imgcontainer">
                <span onclick="document.getElementById('register').style.display='none'" class="close"
                    title="Close Modal">&times;</span>
            </div>

            <div class="container">
                <label for="fname"><b>First Name</b></label>
                <input type="text" placeholder="Enter First Name" name="first_name" required>

                <label for="lname"><b>Last Name</b></label>
                <input type="text" placeholder="Enter Last Name" name="last_name" required>

                <label for="lname"><b>Email</b></label>
                <input type="text" placeholder="Enter Email" name="email" required>

                <label for="address"><b>Address</b></label>
                <input type="text" placeholder="Enter Address" name="address" required>

                <label for="password"><b>Password</b></label>
                <input type="password" placeholder="Enter Password" name="password" required>

                <button type="submit" name="register">Register</button>
            </div>
        </form>
    </div>

    <script>
    var loginModal = document.getElementById('login');
    var registerModal = document.getElementById('register');

    window.onclick = function(event) {
        if (event.target == loginModal) {
            loginModal.style.display = "none";
        }
        if (event.target == registerModal) {
            registerModal.style.display = "none";
        }
    }
    </script>
</body>

</html>