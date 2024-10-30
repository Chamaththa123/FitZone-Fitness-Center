<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    require_once 'src/config/config.php';
}

$blog_query = "SELECT title, summary, author, date FROM blogs";
$blog_result = $conn->query($blog_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Trainers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="public/assets/css/blog.css">
    <style>
    .hero-image {
        background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.3)), url("public/assets/images/StockCake-Gym Ready Gear_1730294309.jpg");
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
            <h1 style="font-size:45px;"><span style='color:#0074D9;background-color:white'>&nbsp;Our&nbsp;</span>
                <span style='color:white;background-color:#0074D9;margin-left:-15px'>&nbsp;Blogs&nbsp;</span>
            </h1>
            <p style="font-size:18px;">
                Meet the experts behind your fitness journey! Our trainers are here to guide, motivate, and help you
                achieve your goals, whether you're just starting out or pushing toward your next big milestone.
            </p>
        </div>
    </div>



    <div class="blog-container">
        <?php if ($blog_result->num_rows > 0): ?>
        <?php while ($blog = $blog_result->fetch_assoc()): ?>
        <div class="card">
            <img src="public/assets/images/StockCake-Serenity in Yoga_1730280569.jpg" alt="Avatar" style="width:100%">
            <div class="container-b">
                <div class='title'><b><?php echo htmlspecialchars($blog['title']); ?></b></div>
                <div class='summary' style='font-size:12px'><?php echo htmlspecialchars($blog['summary']); ?></div>
                <div class='title' style='font-size:12px'><i><?php echo htmlspecialchars($blog['author']); ?></i></div>
                <button>See More</button>
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