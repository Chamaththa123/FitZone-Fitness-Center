<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    require_once 'src/config/config.php';
}

// Fetch memberships
$membership_query = "SELECT * FROM membership";
$membership_result = $conn->query($membership_query);

// Check if user is logged in
$is_logged_in = isset($_SESSION['id']);
$user_id = $is_logged_in ? $_SESSION['id'] : null;

// Handle membership registration and cancellation
$registration_success = $registration_error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($is_logged_in && isset($_POST['register_membership_id'])) {
        // Register for a membership
        $membership_id = (int)$_POST['register_membership_id'];
        
        // Check if the user is already registered for a membership
        $check_query = $conn->prepare("SELECT * FROM users WHERE id = ? AND membership_id IS NOT NULL");
        $check_query->bind_param("i", $user_id);
        $check_query->execute();
        $check_result = $check_query->get_result();
        
        if ($check_result->num_rows > 0) {
            $registration_error = "You are already registered with a membership plan.";
        } else {
            // Register the user for the selected membership
            $register_query = $conn->prepare("UPDATE users SET membership_id = ? WHERE id = ?");
            $register_query->bind_param("ii", $membership_id, $user_id);
            
            if ($register_query->execute()) {
                $registration_success = "Successfully registered for the membership plan.";
            } else {
                $registration_error = "Error registering for the membership. Please try again.";
            }
            
            $register_query->close();
        }
        
        $check_query->close();
    } elseif ($is_logged_in && isset($_POST['cancel_membership'])) {
        // Cancel the membership
        $cancel_query = $conn->prepare("UPDATE users SET membership_id = NULL WHERE id = ?");
        $cancel_query->bind_param("i", $user_id);
        
        if ($cancel_query->execute()) {
            $registration_success = "Membership cancelled successfully.";
        } else {
            $registration_error = "Error cancelling membership. Please try again.";
        }
        
        $cancel_query->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Membership Plans</title>
    <link rel="stylesheet" href="public/assets/css/class.css">
    <style>
    .hero-image {
        background-image: linear-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.1)), url("public/assets/images/StockCake-Home Gym Setup_1730541852.jpg");
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

    @media only screen and (max-width: 768px) {
        .hero-text {
            font-size: 1em;
            width: 80%;
        }

        .alert-success,
        .alert-error {
            font-size: 0.9em;
            padding: 10px;
        }

        .classname {
            font-size: 1.1em;
        }

        .details {
            font-size: 0.85em;
        }

        .reg-btn {
            padding: 8px;
            font-size: 0.9em;
        }
    }

    @media only screen and (max-width: 480px) {
        .hero-text h1 {
            font-size: 1.8em;
            line-height: 1.2em;
        }

        .class-container {
            margin: 10px;
            flex-direction: column;
            align-items: center;
        }

        .card {
            width: 90%;
        }
    }
    </style>
</head>

<body>
    <?php include 'src/includes/header.php'; ?>

    <div class="hero-image">
        <div class="hero-text">
            <h1 style="font-size:45px;font-weight:650"><span
                    style='color:#0074D9;background-color:white'>&nbsp;Membership&nbsp;</span>
                <span style='color:white;background-color:#0074D9;margin-left:-15px'>&nbsp;Plans&nbsp;</span>
            </h1>
            <p style="font-size:18px;">
                Explore our range of membership plans tailored to suit your needs. Whether you're seeking basic access
                or premium benefits, we have options designed for everyone. Join us and enjoy exclusive perks and a
                seamless experience.
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
        <?php if ($membership_result->num_rows > 0): ?>
        <?php while ($membership = $membership_result->fetch_assoc()): ?>
        <?php
                // Check if the user is already registered for this membership
                $already_registered = false;
                if ($is_logged_in) {
                    $check_query = $conn->prepare("SELECT * FROM users WHERE id = ? AND membership_id = ?");
                    $check_query->bind_param("ii", $user_id, $membership['id']);
                    $check_query->execute();
                    $check_result = $check_query->get_result();
                    $already_registered = $check_result->num_rows > 0;
                    $check_query->close();
                }
            ?>
        <div class="card">
            <div class="container-c">
                <div class='classname'><b><?php echo htmlspecialchars($membership['PlanName']); ?></b></div>
                <div class='details'>Price : <?php echo htmlspecialchars($membership['Price']); ?> USD</div>
                <div class='details'>Duration : <?php echo htmlspecialchars($membership['Duration']); ?></div>
                <div class='details'>Benefits : <br /><?php echo htmlspecialchars($membership['Benefits']); ?></div>
                <div class='details'>Special Promotions :
                    <br /><?php echo htmlspecialchars($membership['SpecialPromotions']); ?>
                </div>
                <div class='details'>Description : <br /><?php echo htmlspecialchars($membership['Description']); ?>
                </div>

                <?php if ($is_logged_in): ?>
                <?php if ($already_registered): ?>
                <form method="post" action="">
                    <input type="hidden" name="cancel_membership" value="1">
                    <button type="submit" class='reg-btn'
                        style='width:170px;background-color:#dc3545;font-size:13px'>Cancel
                        Membership</button>
                </form>
                <p class="already-registered"><i>Already Registered &nbsp;&nbsp;&nbsp;</i></p>
                <?php else: ?>
                <form method="post" action="">
                    <input type="hidden" name="register_membership_id" value="<?php echo $membership['id']; ?>">
                    <button type="submit" class='reg-btn'>Register</button>
                </form>
                <?php endif; ?>
                <?php else: ?>
                <p class="please-log-in"><i>Please log in to register for a membership.</i></p>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <p>No membership plans found.</p>
        <?php endif; ?>
    </div>
    <?php include 'src/includes/footer.php'; ?>
</body>

</html>