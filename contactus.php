<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    require_once 'src/config/config.php';
}

// Process contact form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_contact'])) {
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    // Basic validation
    if (empty($subject) || empty($message)) {
        $error_message = "Subject and message fields cannot be empty.";
    } else {
        // Check if the user is logged in
        if (isset($_SESSION['id'])) {
            $customer_id = $_SESSION['id'];  // Get customer ID from session

            // Prepare and execute the SQL query to insert contact message
            $stmt = $conn->prepare("INSERT INTO contact_us (customer_id, subject, message, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iss", $customer_id, $subject, $message);

            if ($stmt->execute()) {
                $contact_success_message = "Your message has been sent successfully!";
            } else {
                $contact_error_message = "Error sending message: " . $stmt->error;
            }

            // Close statement after execution
            $stmt->close();
        } else {
            $contact_error_message = "You must be logged in to send a message.";
        }
    }
}

// Fetch messages for the logged-in user with pagination
$messages_per_page = 3;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $messages_per_page;
if (isset($_SESSION['id'])) {
    $customer_id = $_SESSION['id'];

// Fetch the user's messages with a limit of 3 per page
$stmt = $conn->prepare("SELECT subject, message, reply, created_at FROM contact_us WHERE customer_id = ? ORDER BY created_at DESC LIMIT ?, ?");
$stmt->bind_param("iii", $customer_id, $offset, $messages_per_page);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
$stmt->close();

} else {
    $contact_error_message = "You must be logged in to send a message.";
}


// Fetch the total number of messages to calculate total pages
$stmt_total = $conn->prepare("SELECT COUNT(*) AS total_messages FROM contact_us WHERE customer_id = ?");
$stmt_total->bind_param("i", $customer_id);
$stmt_total->execute();
$total_result = $stmt_total->get_result()->fetch_assoc();
$total_messages = $total_result['total_messages'];
$total_pages = ceil($total_messages / $messages_per_page);
$stmt_total->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My PHP Project</title>
    <!-- <link rel="stylesheet" href="public/assets/css/styles.css"> Link to your CSS file -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
    body,
    html {
        height: 100%;
        margin: 0;
    }

    .hero-image {
        background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.3)), url("public/assets/images/StockCake-Yoga Class Balance_1729572547.jpg");
        height: 70%;
        width: 100%;
        background-repeat: no-repeat;
        background-size: cover;
        position: relative;
    }

    .hero-text {
        text-align: center;
        position: absolute;
        top: 50%;
        left: 50%;
        width: 65%;
        transform: translate(-50%, -50%);
        color: white;
    }

    .hero-text button {
        border: none;
        outline: 0;
        display: inline-block;
        padding: 15px 25px;
        color: white;
        font-size: 18px;
        font-weight: 700;
        background-color: #d91f26;
        text-align: center;
        cursor: pointer;
        width: auto;
        border-radius: 8px;
    }

    .container-contact {
        display: flex;
        justify-content: space-between;
    }

    .column-contact {
        flex: 1;
        padding: 50px;
        box-sizing: border-box;
    }

    .column-contact:not(:last-child) {
        margin-right: 10px;
    }

    .column-1 {
        /* background-color: lightcoral; */
    }

    .column-2 {
        /* background-color: lightgreen; */
    }

    .alert-success {
        padding: 20px;
        background-color: #00ed24;
        color: white;
        margin-bottom: 20px
    }

    .alert-error {
        padding: 20px;
        background-color: #ff4c4c;
        color: white;
        margin-bottom: 20px
    }

    .closebtn {
        margin-left: 15px;
        color: white;
        font-weight: bold;
        float: right;
        font-size: 22px;
        line-height: 20px;
        cursor: pointer;
        transition: 0.3s;
    }

    .closebtn:hover {
        color: black;
    }

    .accordion {
        background-color: #eee;
        color: #444;
        cursor: pointer;
        padding: 18px;
        width: 100%;
        border: none;
        text-align: left;
        outline: none;
        font-size: 15px;
        transition: 0.4s;
    }

    .active,
    .accordion:hover {
        background-color: #ccc;
    }

    .panel {
        padding: 0 18px;
        display: none;
        background-color: white;
        overflow: hidden;
    }

    .message-date {
        font-size: 12px;
    }

    .pagination {
        display: flex;
        justify-content: center;
        padding: 10px;
    }

    .pagination a {
        margin: 0 5px;
        padding: 8px 16px;
        text-decoration: none;
        background-color: #f4f4f4;
        border: 1px solid #ccc;
        color: #333;
        border-radius: 4px;
    }

    .pagination a.active {
        background-color: #4CAF50;
        color: white;
    }

    .pagination a:hover:not(.active) {
        background-color: #ddd;
    }
    </style>
</head>

<body>

    <!-- Header Section -->
    <?php include 'src/includes/header.php'; ?>
    <!-- Main Content Section -->
    <div class="hero-image">
        <div class="hero-text">
            <h1 style="font-size:45px;"><span style='color:#0074D9;background-color:white'>&nbsp;Contact&nbsp;</span>
                <span style='color:white;background-color:#0074D9;margin-left:-15px'>&nbsp;Us&nbsp;</span>
            </h1>
            <p style="font-size:18px;">
                Whether you have questions about memberships, classes, or just want to know more about our fitness
                center, we’re here to help!</p>
        </div>
    </div>


    <div class="container-contact">
        <div class="column-contact column-1">
            <h2>GET IN TOUCH WITH US</h2>

            <?php if (isset($contact_success_message)): ?>
            <div class="alert-success">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                <?php echo htmlspecialchars($contact_success_message); ?>
            </div>
            <?php endif; ?>
            <?php if (isset($contact_error_message)): ?>
            <div class="alert-error">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                <?php echo htmlspecialchars($contact_error_message); ?>
            </div>
            <?php endif; ?>

            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div>
                    <label for="subject"><b>Subject</b></label>
                    <input type="text" placeholder="Enter subject" name="subject" required>

                    <label for="password"><b>Message</b></label>
                    <input type="text" placeholder="Enter message" name="message" required>

                    <button type="submit" name="submit_contact">Register</button>
                </div>
            </form>

            <div>
                <h3>My Messages</h3>

                <?php if (!empty($messages)): ?>
                <?php foreach ($messages as $msg): ?>
                <button class="accordion"><?php echo htmlspecialchars($msg['subject']); ?>
                </button>
                <div class="panel">
                    <p><strong>Message:</strong> <?php echo htmlspecialchars($msg['message']); ?></p>
                    <?php if (!empty($msg['reply'])): ?>
                    <p><strong>Reply:</strong> <?php echo htmlspecialchars($msg['reply']); ?></p>
                    <?php else: ?>
                    <p><em>No reply yet.</em></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <p>No messages found.</p>
                <?php endif; ?>

                <div class="pagination">
                    <?php if ($current_page > 1): ?>
                    <a href="?page=<?php echo $current_page - 1; ?>">&laquo; Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>"
                        class="<?php echo $i == $current_page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>

                    <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?php echo $current_page + 1; ?>">Next &raquo;</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="column-contact column-2">
            <img src='public/assets/images/StockCake-Group Workout Session_1729583555.jpg'
                style='width:100% ;margin-top:40px;border-radius:15px' alt='contact image'>
        </div>
    </div>

    <script>
    var acc = document.getElementsByClassName("accordion");
    var i;

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var panel = this.nextElementSibling;
            if (panel.style.display === "block") {
                panel.style.display = "none";
            } else {
                panel.style.display = "block";
            }
        });
    }
    </script>
    <?php include 'src/includes/footer.php'; ?>
</body>

</html>