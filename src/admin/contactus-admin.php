<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
if (!isset($conn)) {
    require '../config/config.php';  // Database connection
}

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_reply'])) {
    $reply = $_POST['reply'];
    $contact_id = $_POST['contact_id'];

    // Update reply in the contact_us table
    $stmt = $conn->prepare("UPDATE contact_us SET reply = ? WHERE id = ?");
    $stmt->bind_param("si", $reply, $contact_id);

    if ($stmt->execute()) {
        $success_message = "Reply sent successfully!";
    } else {
        $error_message = "Failed to send reply: " . $stmt->error;
    }
}

// Fetch contact us data
$query = "SELECT c.id, c.subject, c.message, c.reply, u.first_name, u.last_name 
          FROM contact_us c 
          JOIN users u ON c.customer_id = u.id
          ORDER BY c.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-XXXXX" crossorigin="anonymous">
    <link rel="stylesheet" href="../../public/assets/css/sidebar.css">
    <link rel="stylesheet" href="../../public/assets/css/table.css">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

    body {
        margin: 0;
        font-family: 'Poppins', Arial, Helvetica, sans-serif;
    }

    .custom-container {
        background-color: white;
        padding: 5px 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }

    .header-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }


    .border-radius {
        border-radius: 20px;
        padding: 40px 20px
    }

    .input-group {
        display: flex;
        gap: 20px;
        margin-bottom: 15px;
    }

    .input-item {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    /* Responsive layout */
    @media (max-width: 600px) {
        .input-group {
            flex-direction: column;
        }
    }

    .reply {
        border-radius: 10px;
        padding: 10px;
        border: 1px solid #ccc;
        font-size: 14px;
        width: 100%;
        resize: vertical;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .reply-button {
        background-color: #00af00;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 5px 7px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-size: 12px;
    }
    </style>
</head>

<body>

    <div class="sidebar">
        <img src="../../public/assets/images/logo.jpg" class='logo'
            style='width:150px;height:100px; display:block; margin: 30px auto;border-radius:10%' alt="Logo">

        <a class="" href="membership-admin.php">Memberships</a>
        <a class='active' ref="contactus-admin.php">Contact Us</a>
        <a href="trainer-admin.php">Trainers</a>
        <a href="class-admin.php">Classes</a>
        <a href="appointment-admin.php">Appointments</a>
        <a href="blogs-admin.php">Blog</a>
    </div>

    <div class="content">
        <div class="container custom-container">
            <div class="header-section">
                <h3 style='font-weight:600'>Contact Us</h3>
            </div>

        </div>

        <div class="container custom-container">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Reply</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td data-label='Customer'>" . htmlspecialchars($row['first_name'] ?? '') . " " . htmlspecialchars($row['last_name'] ?? '') . "</td>";
                                    echo "<td data-label='Subject'>" . htmlspecialchars($row['subject'] ?? '') . "</td>";
                                    echo "<td data-label='Message'>" . htmlspecialchars($row['message'] ?? '') . "</td>";
                                    echo "<td data-label='Reply'>" . htmlspecialchars($row['reply'] ?? 'No reply yet') . "</td>";
                                    echo "<td>";
                                    echo "<form method='POST' action='" . $_SERVER['PHP_SELF'] . "'>";
                                    echo "<textarea name='reply' placeholder='Enter your reply' class='reply' required>" . htmlspecialchars($row['reply'] ?? '') . "</textarea>";
                                    echo "<input type='hidden' name='contact_id' value='" . $row['id'] . "' />";
                                    echo "<button type='submit' name='submit_reply' class='reply-button'>Send Reply</button>";
                                    echo "</form>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No messages found.</td></tr>";
                            }
?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-XXXXX"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-XXXXX"
        crossorigin="anonymous"></script>
</body>

</html>