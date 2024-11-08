<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    require_once 'src/config/config.php';
}

// Fetch trainer data for the select dropdown
$trainer_query = "SELECT id, full_name FROM trainers";
$trainer_result = $conn->query($trainer_query);
$trainers = [];
if ($trainer_result) {
    while ($trainer = $trainer_result->fetch_assoc()) {
        $trainers[] = $trainer;
    }
}

// Process appointment form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_appointment'])) {
    $phone = trim($_POST['phone']);
    $session_type = trim($_POST['session_type']);
    $date = trim($_POST['date']);
    $time = trim($_POST['time']);
    $trainerId = trim($_POST['trainerId']);
    $description = trim($_POST['description']);

    // Basic validation
    if (empty($phone) || empty($session_type) || empty($date) || empty($time) || empty($trainerId) || empty($description)) {
        $appointment_error_message = "All fields are required.";
    } else {
        // Check if the user is logged in
        if (isset($_SESSION['id'])) {
            $customer_id = $_SESSION['id'];  // Get customer ID from session

            // Prepare and execute the SQL query to insert appointment
            $stmt = $conn->prepare("INSERT INTO appointment (customer_id, phone, session_type, date, time, trainerId, description, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("isssssi", $customer_id, $phone, $session_type, $date, $time, $trainerId, $description);



            if ($stmt->execute()) {
                $appointment_success_message = "Your appointment has been booked successfully!";
            } else {
                $appointment_error_message = "Error booking appointment: " . $stmt->error;
            }

            // Close statement after execution
            $stmt->close();
        } else {
            $appointment_error_message = "You must be logged in to book an appointment.";
        }
    }
}

// Fetch appointments for the logged-in user with pagination
$appointments_per_page = 6;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $appointments_per_page;

if (isset($_SESSION['id'])) {
    $customer_id = $_SESSION['id'];

    // Fetch the user's appointments with a limit of 3 per page
    $stmt = $conn->prepare(
        "SELECT a.session_type, a.date, a.time,a.status, a.description, a.created_at, a.phone, t.full_name AS trainer_name
         FROM appointment a
         JOIN trainers t ON a.trainerId = t.id
         WHERE a.customer_id = ?
         ORDER BY a.created_at DESC
         LIMIT ?, ?"
    );
    $stmt->bind_param("iii", $customer_id, $offset, $appointments_per_page);
    $stmt->execute();
    $result = $stmt->get_result();

    $appointments = [];
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
    $stmt->close();

    // Fetch the total number of appointments to calculate total pages
    $stmt_total = $conn->prepare("SELECT COUNT(*) AS total_appointments FROM appointment WHERE customer_id = ?");
    $stmt_total->bind_param("i", $customer_id);
    $stmt_total->execute();
    $total_result = $stmt_total->get_result()->fetch_assoc();
    $total_appointments = $total_result['total_appointments'];
    $total_pages = ceil($total_appointments / $appointments_per_page);
    $stmt_total->close();
} else {
    $appointment_error_message = "You must be logged in to view appointments.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My PHP Project</title>
    <link rel="stylesheet" href="public/assets/css/appointment.css">
    <meta name=" viewport" content="width=device-width, initial-scale=1">

    <style>
    body,
    html {
        height: 100%;
        margin: 0;
    }

    .hero-image {
        background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.1)), url("public/assets/images/StockCake-Yoga Class Session_1731064451.jpg");
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
        background-color: #0074D9;
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
            <h1 style="font-size:45px;font-weight:650"><span
                    style='color:#0074D9;background-color:white'>&nbsp;Contact&nbsp;</span>
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

            <?php if (isset($appointment_success_message)): ?>
            <div class="alert-success">
                <?php echo htmlspecialchars($appointment_success_message); ?>
            </div>
            <?php endif; ?>
            <?php if (isset($appointment_error_message)): ?>
            <div class="alert-error">
                <?php echo htmlspecialchars($appointment_error_message); ?>
            </div>
            <?php endif; ?>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <label for="phone">Phone:</label>
                <input type="text" name="phone" required>

                <label for="session_type">Session Type:</label>
                <select name="session_type" required>
                    <option value="">Select Session Type</option>
                    <option value="Nutrition Counseling">Nutrition Counseling</option>
                    <option value="Personalized Training Session">Personalized Training Session</option>
                </select>


                <label for="date">Date:</label>
                <input type="date" name="date" required>

                <label for="time">Time:</label>
                <input type="time" name="time" required>

                <label for="trainerId">Trainer:</label>
                <select name="trainerId" required>
                    <option value="">Select Trainer</option>
                    <?php foreach ($trainers as $trainer): ?>
                    <option value="<?php echo htmlspecialchars($trainer['id']); ?>">
                        <?php echo htmlspecialchars($trainer['full_name']); ?></option>
                    <?php endforeach; ?>
                </select>


                <div class="input-item">
                    <label for="description">Description:</label>
                    <textarea name="description" rows="3" required></textarea>
                </div>

                <button type="submit" name="submit_appointment">Book Appointment</button>
            </form>
        </div>
        <div class="column-contact column-2">
            <h3>My Appointments</h3>

            <?php if (!empty($appointments)): ?>
            <?php foreach ($appointments as $appt): ?>
            <div>
                <button class="accordion"><?php echo htmlspecialchars($appt['session_type']); ?></button>
                <div class="panel">
                    <p><b>Description : </b><?php echo htmlspecialchars($appt['description']); ?></p>
                    <p><b>Scheduled Date /Time:</b>
                        <?php echo htmlspecialchars($appt['date']); ?>&nbsp;<?php echo htmlspecialchars($appt['time']); ?>
                    </p>
                    <p><b>Phone:</b> <?php echo htmlspecialchars($appt['phone']); ?></p>
                    <p><b>Booked on:</b> <?php echo htmlspecialchars($appt['created_at']); ?></p>
                    <p><b>Status:</b> <?php echo htmlspecialchars($appt['status']); ?></p>
                </div>

            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <p>No appointments found.</p>
            <?php endif; ?>

            <?php if (!empty($appointments) && $total_appointments > 0): ?>
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
            <?php endif; ?>

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