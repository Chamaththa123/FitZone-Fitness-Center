<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    require_once 'src/config/config.php';
}

// Check if this is an AJAX request to fetch blog details
if (isset($_GET['blog_id'])) {
    $id = intval($_GET['blog_id']);
    $stmt = $conn->prepare("SELECT title, content, author, date FROM blogs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $blog = $result->fetch_assoc();

    header('Content-Type: application/json');
    echo json_encode($blog);
    exit;
}

$blog_query = "SELECT id,title, summary, author, date FROM blogs";
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

    .custom-container {
        background-color: white;
        padding: 5px 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
        padding-bottom: 20px;
    }

    #blogView {
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
    </style>
</head>

<body>
    <?php include 'src/includes/header.php'; ?>

    <div class="hero-image">
        <div class="hero-text">
            <h1 style="font-size:45px;font-weight:650"><span
                    style='color:#0074D9;background-color:white'>&nbsp;Our&nbsp;</span>
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
                <button onclick="showBlogDetails(<?php echo $blog['id']; ?>)">See More</button>
            </div>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <p>No trainers found.</p>
        <?php endif; ?>
    </div>

    <div id="blogView" class="w3-modal">
        <div class="w3-modal-content w3-animate-top border-radius">
            <div class="w3-container">
                <span onclick="document.getElementById('blogView').style.display='none'"
                    class="w3-button w3-display-topright"
                    style="border-radius: 20px;margin-top:10px;margin-right:10px">&times;</span>
                <h3 id="blogTitle" style='font-weight:500'></h3>
                <p id="blogContent"></p>
                <p><strong>Author:</strong> <span id="blogAuthor"></span></p>
                <p><strong>Date:</strong> <span id="blogDate"></span></p>
            </div>
        </div>
    </div>
    <script>
    function showBlogDetails(blogId) {
        fetch('?blog_id=' + blogId)
            .then(response => response.json())
            .then(data => {
                document.getElementById('blogTitle').innerText = data.title;
                document.getElementById('blogContent').innerText = data.content;
                document.getElementById('blogAuthor').innerText = data.author;
                document.getElementById('blogDate').innerText = data.date;

                document.getElementById('blogView').style.display = 'flex';
            })
            .catch(error => console.error('Error fetching blog details:', error));
    }

    function closeBlogView() {
        document.getElementById('blogView').style.display = 'none';
    }
    </script>
    <?php include 'src/includes/footer.php'; ?>
</body>

</html>