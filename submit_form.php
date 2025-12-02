<?php
// PHP Script to handle form submission and save data to your MySQL database.

// 1. Database Configuration for XAMPP
// ** Ensure your XAMPP Apache and MySQL servers are running **
$db_host = 'localhost';   
$db_user = 'root';        // Default XAMPP user
$db_pass = '';            // Default XAMPP password (usually empty)
$db_name = 'portfolio_db'; 
$table_name = 'contact_messages'; 

// 2. Check if the form was submitted using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 3. Connect to the database
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    // Check connection for errors
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        // Redirect back to the form page with an error status
        header("Location: index.html#Contact?status=error&msg=" . urlencode("Database connection failed."));
        exit();
    }

    // 4. Sanitize and Collect input data
    $name = isset($_POST['name']) ? trim($conn->real_escape_string($_POST['name'])) : '';
    $email = isset($_POST['email']) ? trim($conn->real_escape_string($_POST['email'])) : '';
    $subject = isset($_POST['subject']) ? trim($conn->real_escape_string($_POST['subject'])) : '';
    $message = isset($_POST['message']) ? trim($conn->real_escape_string($_POST['message'])) : '';

    // Basic server-side validation
    if (empty($name) || empty($email) || empty($subject) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: index.html#Contact?status=error&msg=" . urlencode("Please ensure all fields are filled out correctly and the email is valid."));
        exit();
    }
    
    // 5. Prepare SQL INSERT Statement
    $sql = "INSERT INTO $table_name (name, email, subject, message) VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        error_log("SQL preparation failed: " . $conn->error);
        header("Location: index.html#Contact?status=error&msg=" . urlencode("System error: Could not prepare statement."));
        exit();
    }

    // Bind parameters
    $stmt->bind_param("ssss", $name, $email, $subject, $message);
    
    // 6. Execute the statement
    if ($stmt->execute()) {
        // *** SUCCESS REDIRECTION: Now redirecting without the message parameter ***
        header("Location: index.html#Contact?status=success"); 
        exit();
    } else {
        // Failure
        error_log("SQL execution failed: " . $stmt->error);
        header("Location: index.html#Contact?status=error&msg=" . urlencode("Could not save message. Please try again."));
        exit();
    }

    // 7. Close resources
    $stmt->close();
    $conn->close();

} else {
    // If someone accesses the script directly, redirect them to the home page
    header("Location: index.html");
    exit();
}
?>