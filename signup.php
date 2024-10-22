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


