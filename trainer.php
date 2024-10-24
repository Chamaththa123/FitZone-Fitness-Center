<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    require_once 'src/config/config.php';
}

$trainer_query = "SELECT full_name, certification, specialties, experience, description FROM trainers";
$trainer_result = $conn->query($trainer_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Trainers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="public/assets/css/trainer.css">
    <style>
    .hero-image {
        background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.3)), url("public/assets/images/rendered_1400x.progressive.webp");
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
            <h1 style="font-size:55px">Our Trainers</h1>
            <p style="font-size:20px;">
                Meet the experts behind your fitness journey! Our trainers are here to guide, motivate, and help you
                achieve your goals, whether you're just starting out or pushing toward your next big milestone.
            </p>
        </div>
    </div>

    <div class='des'>Our team of dedicated and certified trainers is here to guide you every step of the way on
        your
        fitness
        journey. Whether you're a beginner looking for support or a seasoned athlete aiming to reach new heights, our
        trainers bring the expertise, motivation, and personalized attention you need to achieve your goals. From
        strength training to specialized classes, we believe in creating an environment where you can thrive, push your
        limits, and become the best version of yourself.</div>

    <div class="trainer-container">
        <?php if ($trainer_result->num_rows > 0): ?>
        <?php while ($trainer = $trainer_result->fetch_assoc()): ?>
        <div class="flip-card">
            <div class="flip-card-inner">
                <div class="flip-card-front">
                    <img src="public/assets/images/logo.jpg" alt="Trainer Image" class="trainer-img">
                    <div class="trainer-name"><?php echo htmlspecialchars($trainer['full_name']); ?></div>
                    <div class="trainer-cert"><?php echo htmlspecialchars($trainer['certification']); ?></div>
                    <div class="trainer-specialty"><?php echo htmlspecialchars($trainer['specialties']); ?></div>
                </div>
                <div class="flip-card-back">
                    <h2><?php echo htmlspecialchars($trainer['full_name']); ?></h2>
                    <p>Experience: <?php echo htmlspecialchars($trainer['experience']); ?> years</p>
                    <p><?php echo htmlspecialchars($trainer['description']); ?></p>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <p>No trainers found.</p>
        <?php endif; ?>
    </div>
</body>

</html>