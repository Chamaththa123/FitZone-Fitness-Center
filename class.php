<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    require_once 'src/config/config.php';
}

// Fetch classes with trainers
$class_query = "SELECT class.*, trainers.full_name AS TrainerName 
                FROM class 
                JOIN trainers ON class.TrainerID = trainers.id";
$trainer_result = $conn->query($class_query);

// Check if user is logged in
$is_logged_in = isset($_SESSION['id']);
$customer_id = $is_logged_in ? $_SESSION['id'] : null;

// Initialize messages
$registration_success = null;
$registration_error = null;

// Handle class registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_class_id'])) {
    $class_id = $_POST['register_class_id'];

    if ($is_logged_in) {
        // Check if the user is already registered for the class
        $check_query = $conn->prepare("SELECT * FROM customer_has_class WHERE customer_id = ? AND class_id = ?");
        $check_query->bind_param("ii", $customer_id, $class_id);
        $check_query->execute();
        $check_result = $check_query->get_result();

        if ($check_result->num_rows > 0) {
            $_SESSION['registration_error'] = "You are already registered for this class.";
        } else {
            // Register the customer for the class
            $stmt = $conn->prepare("INSERT INTO customer_has_class (customer_id, class_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $customer_id, $class_id);

            if ($stmt->execute()) {
                $_SESSION['registration_success'] = "Successfully registered for the class!";
            } else {
                $_SESSION['registration_error'] = "Error registering for the class: " . $stmt->error;
            }
            $stmt->close();
        }
        $check_query->close();

        // Redirect to avoid form resubmission on refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $_SESSION['registration_error'] = "You must be logged in to register for a class.";
    }
}

// Retrieve and clear any session messages
if (isset($_SESSION['registration_success'])) {
    $registration_success = $_SESSION['registration_success'];
    unset($_SESSION['registration_success']);
}
if (isset($_SESSION['registration_error'])) {
    $registration_error = $_SESSION['registration_error'];
    unset($_SESSION['registration_error']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Classes</title>
    <link rel="stylesheet" href="public/assets/css/class.css">
    <style>
    .hero-image {
        background-image: linear-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.1)), url("public/assets/images/StockCake-Yoga Class Serenity_1730309626.jpg");
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

    .alert-success,
    .alert-error {
        padding: 20px;
        margin: 20px;
        color: white;
        border-radius: 5px;
    }

    .alert-success {
        background-color: #28a745;
    }

    .alert-error {
        background-color: #dc3545;
    }

    @media (max-width: 768px) {
        .hero-text h1 {
            font-size: 35px;
        }

        .hero-text p {
            font-size: 16px;
        }

        .class-container {
            flex-direction: column;
            align-items: center;
        }

        .card {
            width: 90%;
            margin: 10px;
        }

        .container-c {
            padding: 15px;
        }

        .details {
            font-size: 14px;
        }

        .reg-btn {
            width: 100%;
            padding: 12px;
            font-size: 16px;
        }
    }

    @media (max-width: 480px) {
        .hero-text h1 {
            font-size: 28px;
        }

        .hero-text p {
            font-size: 14px;
        }

        .container-c {
            padding: 10px;
        }

        .details {
            font-size: 13px;
        }
    }
    </style>
</head>

<body>
    <?php include 'src/includes/header.php'; ?>

    <div class="hero-image">
        <div class="hero-text">
            <h1 style="font-size:45px;font-weight:650"><span
                    style='color:#0074D9;background-color:white'>&nbsp;Our&nbsp;</span>
                <span style='color:white;background-color:#0074D9;margin-left:-15px'>&nbsp;Classes&nbsp;</span>
            </h1>
            <p style="font-size:18px;">
                Discover our range of yoga classes designed for all levels and goals. Join us on a journey of growth,
                peace, and renewal.
            </p>
        </div>
    </div>
    <div>

        <?php if ($registration_success): ?>
        <div class="alert-success"><?php echo htmlspecialchars($registration_success); ?></div>
        <?php endif; ?>
        <?php if ($registration_error): ?>
        <div class="alert-error"><?php echo htmlspecialchars($registration_error); ?></div>
        <?php endif; ?>
    </div>
    <div class="class-container">


        <?php if ($trainer_result->num_rows > 0): ?>
        <?php while ($trainer = $trainer_result->fetch_assoc()): ?>
        <?php
            // Check if the customer is already registered for this class
            $already_registered = false;
            if ($is_logged_in) {
                $check_query = $conn->prepare("SELECT * FROM customer_has_class WHERE customer_id = ? AND class_id = ?");
                $check_query->bind_param("ii", $customer_id, $trainer['id']);
                $check_query->execute();
                $check_result = $check_query->get_result();
                $already_registered = $check_result->num_rows > 0;
                $check_query->close();
            }
        ?>
        <div class="card">
            <div class="container-c">
                <div class='day'><b><span class='day-text'><?php echo htmlspecialchars($trainer['Day']); ?></span></b>
                </div>
                <div class='classname'><b><?php echo htmlspecialchars($trainer['ClassName']); ?></b></div>
                <div class='details'>Time : <?php echo htmlspecialchars($trainer['StartTime']); ?> -
                    <?php echo htmlspecialchars($trainer['EndTime']); ?></div>
                <div class='details'>Trainer : <?php echo htmlspecialchars($trainer['TrainerName']); ?></div>
                <div class='details'>Mode : <?php echo htmlspecialchars($trainer['Mode']); ?></div>
                <div class='details'>Class Details : <br /><?php echo htmlspecialchars($trainer['Description']); ?>
                </div>

                <?php if ($is_logged_in): ?>
                <?php if ($already_registered): ?>
                <p class="already-registered"><i>Already Registered</i></p>
                <?php else: ?>
                <form method="post" action="">
                    <input type="hidden" name="register_class_id" value="<?php echo $trainer['id']; ?>">
                    <button type="submit" class='reg-btn'>Register</button>
                </form>
                <?php endif; ?>
                <?php else: ?>
                <p class="please-log-in"><i>Please log in to register for classes.</i></p>
                <?php endif; ?>

            </div>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <p>No classes found.</p>
        <?php endif; ?>
    </div>
    <?php include 'src/includes/footer.php'; ?>
</body>

</html>