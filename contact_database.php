<?php
// Enable error reporting (for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details (Update these)
$servername = "127.0.0.1"; // Change if necessary
$username = "u103912025_iyappan6379";
$password = "easccsCyberSecurity@3";
$dbname = "u103912025_contact_form";

// Admin Email (Where you will receive notifications)
$admin_email = "easc.cscs@gmail.com"; // Change this to your email
$admin_email = "departmentofcswithcs@gmail.com";
// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data exists before using it
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['contact_name']) && !empty($_POST['contact_email']) && !empty($_POST['contact_message'])) {
        
        // Get form data safely
        $name = htmlspecialchars($_POST['contact_name']);
        $phone = htmlspecialchars($_POST['contact_phone']);
        $email = htmlspecialchars($_POST['contact_email']);
        $message = htmlspecialchars($_POST['contact_message']);

        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO contacts (name, phone, email, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $phone, $email, $message);

        // Execute and check if successful
        if ($stmt->execute()) {
            // Send Email Notification
            $subject = "New Contact Form Submission";
            $email_message = "You have received a new message from your website contact form:\n\n";
            $email_message .= "Name: $name\n";
            $email_message .= "Phone: $phone\n";
            $email_message .= "Email: $email\n";
            $email_message .= "Message:\n$message\n";

            // Email Headers
            $headers = "From: CyberSecurity@easc-cs-cybersecurity.com\r\n" .
                       "Reply-To: $email\r\n" .
                       "X-Mailer: PHP/" . phpversion();

            // Send Email
            if (mail($admin_email, $subject, $email_message, $headers)) {
                // Redirect to Thank You page
                header("Location: thank-you.html");
                exit();
            } else {
                echo "❌ Error sending email.";
            }
        } else {
            echo "❌ Error: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    } else {
        echo "❌ Please fill in all required fields.";
    }
}

// Close connection
$conn->close();
?>
