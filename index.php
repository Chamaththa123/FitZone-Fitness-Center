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
        background-image: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.2)), url("public/assets/images/StockCake-Gym Running Workout_1729507439.jpg");
        height: 90%;
        background-position: center;
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
        padding: 20px 25px;
        color: white;
        font-size: 18px;
        font-weight: 700;
        background-color: #c00000;
        text-align: center;
        cursor: pointer;
        width: auto;
        border-radius: 8px;
    }
    </style>
</head>

<body>

    <!-- Header Section -->
    <?php include 'src/includes/header.php'; ?>
    <!-- Main Content Section -->
    <div class="hero-image">
        <div class="hero-text">
            <h1 style="font-size:55px">Achieve Your Fitness Goals Today!</h1>
            <p style="font-size:20px;">
                Join FITNESS CLUB and Start Your
                Journey
                Towards a<br />
                Healthier,
                Stronger You!</p>
            <button>Join Us Today</button>
        </div>
    </div>

    <!-- Footer Section -->

    <!-- <script src=" public/assets/js/scripts.js">
                </script> Link to your JS file -->
</body>

</html>