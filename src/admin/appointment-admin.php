<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    require '../config/config.php';
}

// Fetch appointments with related customer and trainer names
$appointment_query = "SELECT appointment.*, CONCAT(users.first_name, ' ', users.last_name) AS CustomerName, trainers.full_name AS TrainerName 
                      FROM appointment 
                      JOIN users ON appointment.customer_id = users.id 
                      JOIN trainers ON appointment.trainerId = trainers.id";
$appointment_result = $conn->query($appointment_query);

// Handle appointment approval
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['approve_appointment'])) {
    $appointment_id = $_POST['id'];
    $stmt = $conn->prepare("UPDATE appointment SET status = 'Approved' WHERE id = ?");
    $stmt->bind_param("i", $appointment_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Appointment approved successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to approve appointment: " . $stmt->error;
    }

    header('Location: appointment-admin.php');
    exit;
}

// Handle appointment rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reject_appointment'])) {
    $appointment_id = $_POST['id'];
    $stmt = $conn->prepare("UPDATE appointment SET status = 'Rejected' WHERE id = ?");
    $stmt->bind_param("i", $appointment_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Appointment rejected successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to reject appointment: " . $stmt->error;
    }

    header('Location: appointment-admin.php');
    exit;
}

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
        padding-bottom: 20px;
    }

    #addClassModal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: none;
        justify-content: center;
        align-items: flex-start;
        padding-top: 10px;
        background-color: rgba(0, 0, 0, 0.4);
        z-index: 1000;
    }

    #editClassModal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: none;
        justify-content: center;
        align-items: flex-start;
        padding-top: 10px;
        background-color: rgba(0, 0, 0, 0.4);
        z-index: 1000;
    }

    .border-radius {
        border-radius: 10px;
        max-width: 800px;
        margin: auto;
    }

    .input-group {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }

    .input-item {
        flex: 1;
    }

    .reply-button {
        background-color: #198754;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 5px 10px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s ease;
        font-size: 12px
    }

    .edit-button,
    .delete-button {
        background-color: #ffc107;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 5px 10px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-size: 12px
    }

    .delete-button {
        background-color: #dc3545;
        font-size: 12px
    }

    .action-buttons {
        display: flex;
        gap: 10px;
    }

    .alert {
        padding: 20px;
        background-color: #f44336;
        color: white;
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

    .add-btn {
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 5px 10px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-size: 14px
    }
    </style>
</head>

<body>

    <div class="sidebar">
        <img src="../../public/assets/images/logo.jpg" class='logo'
            style='width:150px; display:block; margin: 30px auto;' alt="Logo">
        <a class="" href="admin.php">Home</a>
        <a class="" href="membership-admin.php">Memberships</a>
        <a href="contactus-admin.php">Contact Us</a>
        <a href="trainer-admin.php">Trainers</a>
        <a href="class-admin.php">Classes</a>
        <a class="active" href="appointment-admin.php">Appointments</a>
        <a href="blogs-admin.php">Blog</a>
    </div>

    <div class="content">
        <div class="container custom-container">
            <div class="header-section">
                <h3 style='font-weight:600'>Appointments</h3>
            </div>
        </div>

        <div class="container custom-container">
            <?php if (isset($_SESSION['message'])): ?>
            <div class="alert" style="background-color: #4CAF50;">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                <strong>Success!</strong> <?php echo htmlspecialchars($_SESSION['message']); ?>
            </div>
            <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert" style="background-color: #f44336;">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                <strong>Error!</strong> <?php echo htmlspecialchars($_SESSION['error_message']); ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Phone</th>
                            <th>Session Type</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Trainer</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($appointment_result->num_rows > 0) {
                            while ($appointment = $appointment_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td data-label='Customer Name'>" . htmlspecialchars($appointment['CustomerName']) . "</td>";
                                echo "<td data-label='Phone'>" . htmlspecialchars($appointment['phone']) . "</td>";
                                echo "<td data-label='Session Type'>" . htmlspecialchars($appointment['session_type']) . "</td>";
                                echo "<td data-label='Date'>" . htmlspecialchars($appointment['date']) . "</td>";
                                echo "<td data-label='Time'>" . htmlspecialchars($appointment['time']) . "</td>";
                                echo "<td data-label='Trainer'>" . htmlspecialchars($appointment['TrainerName']) . "</td>";
                                echo "<td data-label='Description'>" . htmlspecialchars($appointment['description']) . "</td>";
                                echo "<td data-label='Status'>" . htmlspecialchars($appointment['status']) . "</td>";
                                echo "<td data-label='Action'>
                                        <div class='action-buttons'>
                                            <form method='POST' action='' style='display:inline;'>
                                                <input type='hidden' name='id' value='" . $appointment['id'] . "'>
                                                <button type='submit' name='approve_appointment' class='reply-button'>Approve</button>
                                            </form>
                                            <form method='POST' action='' style='display:inline;'>
                                                <input type='hidden' name='id' value='" . $appointment['id'] . "'>
                                                <button type='submit' name='reject_appointment' class='delete-button'>Reject</button>
                                            </form>
                                        </div>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='9'>No appointments found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-XXXXX"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-XXXXX"
        crossorigin="anonymous"></script>
</body>

</html>