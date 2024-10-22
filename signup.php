<?php
$servername = "vultr-prod-cfbaea5c-14b5-4c87-b3e2-9929eed22e05-vultr-prod-4489.vultrdb.com";
$username = "vultradmin";
$password = "AVNS_M3gApQAtkBF-G3u_8PD";
$dbname = "defaultdb";
$port = 16751; // MySQL port

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = ""; // Initialize an empty error message

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    // Check if the email already exists
    $check_email_query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_email_query);
    if ($stmt === false) {
        die("Error preparing query: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error_message = "This email is already registered. Please use a different email.";
    } else {
        // Check if passwords match
        if ($password === $confirm_password) {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert data into the database with id set to 1
            $sql = "INSERT INTO users (id, name, email, password) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $id = 1; // Assign the default value of 1 to id
            $stmt->bind_param("isss", $id, $name, $email, $hashed_password);

            if ($stmt->execute()) {
                $error_message = "Registered successfully!";
            } else {
                $error_message = "Error: " . $stmt->error;
            }
        } else {
            $error_message = "Passwords do not match!";
        }
    }
    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            margin: 0;
            padding: 0;
        }
        .signup-container {
            width: 100%;
            max-width: 400px;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #1d3557;
            margin-bottom: 20px;
        }
        .signup-container label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        .signup-container input[type="text"],
        .signup-container input[type="email"],
        .signup-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .signup-container input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #1d3557;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .signup-container input[type="submit"]:hover {
            background-color: #457b9d;
        }
        .signup-container .login {
            text-align: center;
            margin-top: 20px;
        }
        .signup-container .login a {
            color: #1d3557;
            text-decoration: none;
        }
        .signup-container .login a:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }
        .success {
            color: green;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Create Your Account</h2>
        <?php if (!empty($error_message)) : ?>
            <div class="<?php echo ($error_message === 'Registered successfully!') ? 'success' : 'error'; ?>">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <form action="signup.php" method="POST">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" placeholder="Enter your full name" required>
            
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter a strong password" required>
            
            <label for="confirm-password">Confirm Password</label>
            <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" required>
            
            <input type="submit" value="Sign Up">
        </form>
    </div>
</body>
</html>
