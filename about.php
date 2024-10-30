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
        background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.1)), url("public/assets/images/StockCake-Group Yoga Session_1730294146.jpg");
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
        flex-wrap: wrap;
    }

    .column-contact {
        flex: 1;
        padding: 20px;
        box-sizing: border-box;
    }

    /* Responsive styling for smaller screens */
    @media (max-width: 768px) {
        .hero-text {
            width: 85%;
            /* Reduce width of hero text for mobile screens */
            padding: 10px;
        }

        .hero-text h1 {
            font-size: 36px;
            /* Adjust font size for mobile */
        }

        .hero-text p {
            font-size: 16px;
            /* Adjust font size for mobile */
        }

        .container-contact {
            flex-direction: column;
        }

        .column-contact {
            padding: 20px;
            width: 100%;
            /* Make columns full width on mobile */
            margin: 0;
        }

        .hero-image {
            height: 50%;
            /* Reduce height for better visibility on mobile */
        }
    }
    </style>

</head>

<body>
    <?php include 'src/includes/header.php'; ?>

    <div class="hero-image">
        <div class="hero-text">
            <h1 style="font-size:45px;font-weight:650"><span
                    style='color:#0074D9;background-color:white'>&nbsp;About&nbsp;</span>
                <span style='color:white;background-color:#0074D9;margin-left:-15px'>&nbsp;Us&nbsp;</span>
            </h1>

        </div>
    </div>

    <div class="container-contact">
        <div class="column-contact column-1">
            <img src='public/assets/images/yoga-fitness-clase.webp'
                style='width:100% ;margin-top:20px;border-radius:15px' alt='contact image'>
        </div>
        <div class="column-contact column-2" style='font-size:14px'>
            <h2 style='color:#0074D9;font-weight:550'>POWER UP YOUR FITNESS WITH FITZONE FITNESS CENTER</h2>
            <div>
                Fitzone Fitness Center pioneered the Sri Lankan gym industry in 1994 and has remained the largest chain
                of
                fitness centres since its inception, earning an undisputed reputation among fitness enthusiasts. We also
                pride ourselves in being able to train both the novice and the elite athlete while specializing in
                providing healthcare services that are both prophylactic and therapeutic to all our members.

                ​<br />
                ​<br />

                Our vision is to bring our fitness centres to you: We currently operate a chain of 24 fitness centres,
                serving over 25,000 members in Colombo and its suburbs. Moreover, with over 100,000 sq ft of gym space,
                Power World offers an expansive and versatile environment for a comprehensive fitness experience.

                ​<br />
                ​<br />
                ​

                At Fitzone Fitness Center, we believe that fitness is a lifestyle. Our passion for fitness and
                commitment to
                our members sets us apart from other gyms. Our trainers are certified experts in their field and they
                will work with you to create a personalised fitness plan based on your goals. Whether you're a beginner
                or a pro, we have the tools and knowledge to help you succeed.
            </div>
        </div>
    </div>
    <?php include 'src/includes/footer.php'; ?>
</body>

</html>