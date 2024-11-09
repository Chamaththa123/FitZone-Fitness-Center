<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    require_once 'src/config/config.php';
}

// Fetch logged-in user details
if (isset($_SESSION['user_email'])) {
    $user_email = $_SESSION['user_email'];
    $user_query = $conn->prepare("SELECT users.*, membership.PlanName 
                                  FROM users 
                                  LEFT JOIN membership ON users.membership_id = membership.id 
                                  WHERE users.email = ?");
    $user_query->bind_param("s", $user_email);
    $user_query->execute();
    $user_result = $user_query->get_result();
    $user = $user_result->fetch_assoc();
}

// Fetch registered classes for the logged-in user
$registered_classes = [];
if (isset($user)) {
    $customer_id = $user['id']; // Assuming 'id' is the customer_id in users table
    $class_query = $conn->prepare("SELECT class.ClassName, class.StartTime, class.EndTime 
                                    FROM customer_has_class 
                                    JOIN class ON customer_has_class.class_id = class.id 
                                    WHERE customer_has_class.customer_id = ?");
    $class_query->bind_param("i", $customer_id);
    $class_query->execute();
    $class_result = $class_query->get_result();

    while ($class = $class_result->fetch_assoc()) {
        $registered_classes[] = $class;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Trainers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="public/assets/css/profile.css">
    <style>
    .hero-image {
        background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.3)), url("public/assets/images/StockCake-Gym Workout Session_1729885760.jpg");
        height: 70vh;
        width: 100%;
        background-repeat: no-repeat;
        background-size: cover;
        position: relative;
        z-index: 1;
    }

    .hero-text {
        text-align: center;
        position: absolute;
        top: 50%;
        left: 50%;
        width: 65%;
        transform: translate(-50%, -50%);
        color: white;
        z-index: 2;
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

    .trainer-card {
        background-color: white;
        width: 60%;
        margin: 20px auto;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 3;
        margin-top: -150px;
    }

    .card-content {
        padding: 20px;
    }

    .card-content h2 {
        margin: 0 0 10px;
        font-size: 24px;
        color: #333;
    }

    .card-content p {
        margin: 0;
        font-size: 16px;
        color: #666;
    }

    .content-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        padding: 20px;
        margin-top: 20px;
    }

    .section {
        padding: 20px;
        /* background-color: #f4f4f4; */
        border-radius: 10px;
    }

    .left-section {
        flex: 2;
        /* Larger section */
        min-width: 300px;
    }

    .right-section {
        flex: 1;
        /* Smaller section */
        min-width: 200px;
    }

    .edit-button,
    .delete-button {
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 8px 10px;
        cursor: pointer;
        width: 100px;
        transition: background-color 0.3s ease;
    }

    .delete-button {
        background-color: #dc3545;
    }

    /* Stack sections vertically on smaller screens */
    @media (max-width: 768px) {

        .left-section,
        .right-section {
            flex-basis: 100%;
            /* Take full width */
        }
    }
    </style>
</head>

<body>
    <?php include 'src/includes/header.php'; ?>

    <div class="hero-image">
        <div class="hero-text">
            <h1 style="font-size:45px;font-weight:650"><span
                    style='color:#0074D9;background-color:white'>&nbsp;User&nbsp;</span>
                <span style='color:white;background-color:#0074D9;margin-left:-15px'>&nbsp;Profile&nbsp;</span>
            </h1>
        </div>
    </div>
    <div class="trainer-card">
        <div class="content-wrapper">
            <div class="section right-section">
                <div style="display: flex; justify-content: center;">
                    <img src="public/assets/images/user.png" class="logo" style="width:160px;" alt="Logo">

                </div>
                <form method="post" action="">
                    <center> <button type='submit' name="logout" class='delete-button'
                            style=' margin-top: 40px;'>Logout</button></center>
                </form>
            </div>
            <div class="section left-section">
                <?php if (isset($user)): ?>
                <h2 style='font-weight:500'>
                    <?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?></h2>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
                <p><strong>Registered Membership Plans</strong>
                <ul>

                    <li><?php echo htmlspecialchars($user['PlanName'] ?? 'No membership plan'); ?>&nbsp; Membership Plan
                    </li>
                </ul>
                </p>
                <p><strong>Registered Classes</strong>
                    <?php if (!empty($registered_classes)): ?>
                <ul>
                    <?php foreach ($registered_classes as $class): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($class['ClassName']); ?></strong>
                        &nbsp;&nbsp;&nbsp;Time:
                        <?php echo htmlspecialchars($class['StartTime']) . " - " . htmlspecialchars($class['EndTime']); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p>You have not registered for any classes.</p>
                <?php endif; ?>

                <?php else: ?>
                <p>No user details available.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>
    <?php include 'src/includes/footer.php'; ?>
</body>

</html>