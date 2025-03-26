<?php
// Enable error reporting for debugging (Remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = "127.0.0.1"; // Change this if needed
$username = "u103912025_iyappan6379";
$password = "easccsCyberSecurity@3";
$dbname = "u103912025_contact_form";

// Admin Email
$admin_email = "departmentofcswithcs@gmail.com"; // Update the admin email address as needed
$admin_email = "easc.cscs@gmail.com"; // Update the admin email address as needed

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debugging: Print received POST data
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    // Check if required fields exist and are not empty
    if (isset($_POST['name'], $_POST['email'], $_POST['phone'], $_POST['course']) &&
        !empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['phone']) && !empty($_POST['course'])) {

        // Sanitize input
        $name = mysqli_real_escape_string($conn, trim($_POST['name']));
        $email = mysqli_real_escape_string($conn, trim($_POST['email']));
        $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
        $course = mysqli_real_escape_string($conn, trim($_POST['course']));

        // Create table if it doesn't exist
        $sql = "CREATE TABLE IF NOT EXISTS admissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            course VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $conn->query($sql); // Create table if not exists

        // Insert Data
        $stmt = $conn->prepare("INSERT INTO admissions (name, email, phone, course) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $phone, $course);

        if ($stmt->execute()) {
            // Send Email Notification
            $subject = "New Admission Inquiry";
            $email_message = "You have received a new admission inquiry:\n\n";
            $email_message .= "Name: $name\nEmail: $email\nPhone: $phone\nCourse: $course\n";

            // Email headers with Content-Type for better formatting
            $headers = "From: noreply@yourdomain.com\r\n"; // Change the 'from' address
            $headers .= "Reply-To: $email\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n"; // Set content type to plain text with UTF-8 encoding

            // Send email
            $mail_sent = mail($admin_email, $subject, $email_message, $headers);

            // Check if the email was sent successfully
            if ($mail_sent) {
                // Redirect to Thank You page
                header("Location: admissionthank.html");
                exit();
            } else {
                echo "❌ Error: Failed to send the email.";
            }

        } else {
            echo "❌ Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "❌ Please fill in all required fields.";
    }
}

// Close connection
$conn->close();
?>
