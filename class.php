<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    require_once 'src/config/config.php';
}

$class_query = "SELECT class.*, trainers.full_name AS TrainerName 
                FROM class 
                JOIN trainers ON class.TrainerID = trainers.id";
$trainer_result = $conn->query($class_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Trainers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
                Discover our range of yoga classes designed for all levels and goals. Whether you're here to find
                balance, build strength, or explore mindfulness, our classes offer a path tailored just for you. Join us
                in a journey of growth, peace, and renewal.
            </p>
        </div>
    </div>


    <div class="class-container">
        <?php if ($trainer_result->num_rows > 0): ?>
        <?php while ($trainer = $trainer_result->fetch_assoc()): ?>
        <div class="card">

            <div class="container-c">
                <div class='day'><b><span class='day-text'><?php echo htmlspecialchars($trainer['Day']); ?></span></b>
                </div>
                <div class='classname'><b><?php echo htmlspecialchars($trainer['ClassName']); ?></b>
                </div>
                <div class='details'>Time : <?php echo htmlspecialchars($trainer['StartTime']); ?> -
                    <?php echo htmlspecialchars($trainer['EndTime']); ?>
                </div>
                <div class='details'>Trainer : <?php echo htmlspecialchars($trainer['TrainerName']); ?>
                </div>
                <div class='details'>Mode : <?php echo htmlspecialchars($trainer['Mode']); ?>
                </div>
                <div class='details'>Class Details : <br /><?php echo htmlspecialchars($trainer['Description']); ?>
                </div>

                <button class='reg-btn'>Register</button>
            </div>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <p>No trainers found.</p>
        <?php endif; ?>
    </div>
    <?php include 'src/includes/footer.php'; ?>
</body>

</html>