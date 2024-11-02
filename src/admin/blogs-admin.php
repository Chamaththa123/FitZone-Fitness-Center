<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    require '../config/config.php';
}

// Handle blog submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_blog'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $summary = $_POST['summary'];
    $author = $_POST['author'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("INSERT INTO blogs (title, content, summary, author, date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $content, $summary, $author, $date);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Blog added successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to add blog: " . $stmt->error;
    }

    header('Location: blogs-admin.php');
    exit;
}

// Handle blog update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_blog'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $summary = $_POST['summary'];
    $author = $_POST['author'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("UPDATE blogs SET title = ?, content = ?, summary = ?, author = ?, date = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $title, $content, $summary, $author, $date, $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Blog updated successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to update blog: " . $stmt->error;
    }

    header('Location: blogs-admin.php');
    exit;
}

// Handle blog deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_blog'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM blogs WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Blog deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to delete blog: " . $stmt->error;
    }

    header('Location: blogs-admin.php');
    exit;
}

// Fetch blog data
$trainer_query = "SELECT id, title, content, summary, author, date FROM blogs";
$trainer_result = $conn->query($trainer_query);
?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

    #addBlogModal {
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

    #editBlogModal {
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
    }

    .delete-button {
        background-color: #dc3545;
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
        <a class="active" href="blogs-admin.php">Blog</a>
    </div>

    <div class="content">
        <div class="container custom-container" style="padding-top:17px;">
            <div class="header-section">
                <h3 style='font-weight:600'>Blogs</h3>
                <button onclick="document.getElementById('addBlogModal').style.display='block'" class="add-btn">Add
                    Blog</button>
            </div>
        </div>

        <div class="container custom-container ">
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
                            <th>Title</th>
                            <th>Content</th>
                            <th>Summary</th>
                            <th>Author</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($trainer_result->num_rows > 0) {
                            while ($blog = $trainer_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td data-label='Title'>" . htmlspecialchars($blog['title']) . "</td>";
                                echo "<td data-label='Content'>" . htmlspecialchars($blog['content']) . "</td>";
                                echo "<td data-label='Summary'>" . htmlspecialchars($blog['summary']) . "</td>";
                                echo "<td data-label='Author'>" . htmlspecialchars($blog['author']) . "</td>";
                                echo "<td data-label='Date'>" . htmlspecialchars($blog['date']) . "</td>";
                                echo "<td data-label='Action'>
                                    <div class='action-buttons'>
                                        <button class='edit-button' onclick='openEditModal(" . $blog['id'] . ", \"" . addslashes($blog['title']) . "\", \"" . addslashes($blog['content']) . "\", \"" . addslashes($blog['summary']) . "\", \"" . addslashes($blog['author']) . "\", \"" . addslashes($blog['date']) . "\")'>Edit</button>
                                        <form method='POST' action='' style='display:inline;'>
                                            <input type='hidden' name='id' value='" . $blog['id'] . "'>
                                            <button type='submit' name='delete_blog' class='delete-button' onclick='return confirm(\"Are you sure you want to delete this blog?\")'>Delete</button>
                                        </form>
                                    </div>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No blogs found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Blog Modal -->
    <div id="addBlogModal" class="w3-modal">
        <div class="w3-modal-content w3-animate-top border-radius">
            <div class="w3-container">
                <span onclick="document.getElementById('addBlogModal').style.display='none'"
                    class="w3-button w3-display-topright"
                    style="border-radius: 20px;margin-top:10px;margin-right:10px">&times;</span>
                <h3>Add Blog</h3>
                <form method="POST" action="">
                    <div class="input-group">
                        <div class="input-item">
                            <label for="title">Title:</label>
                            <input type="text" name="title" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-item">
                            <label for="content">Content:</label>
                            <textarea name="content" required></textarea>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-item">
                            <label for="summary">Summary:</label>
                            <textarea name="summary" required></textarea>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-item">
                            <label for="author">Author:</label>
                            <input type="text" name="author" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-item">
                            <label for="date">Date:</label>
                            <input type="date" style="padding: 12px 20px;" name="date" required>
                        </div>
                    </div>
                    <input type="submit" name="submit_blog" value="Add Blog" class="reply-button">
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Blog Modal -->
    <div id="editBlogModal" class="w3-modal">
        <div class="w3-modal-content w3-animate-top border-radius">
            <div class="w3-container">
                <span onclick="document.getElementById('editBlogModal').style.display='none'"
                    class="w3-button w3-display-topright"
                    style="border-radius: 20px;margin-top:10px;margin-right:10px">&times;</span>
                <h3>Edit Blog</h3>
                <form method="POST" action="">
                    <input type="hidden" name="id" id="edit_blog_id">
                    <div class="input-group">
                        <div class="input-item">
                            <label for="title">Title:</label>
                            <input type="text" name="title" id="edit_blog_title" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-item">
                            <label for="content">Content:</label>
                            <textarea name="content" id="edit_blog_content" rows="2" required></textarea>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-item">
                            <label for="summary">Summary:</label>
                            <textarea name="summary" id="edit_blog_summary" rows="2" required></textarea>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-item">
                            <label for="author">Author:</label>
                            <input type="text" name="author" id="edit_blog_author" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-item">
                            <label for="date">Date:</label>
                            <input type="date" style="padding: 12px 20px;" name=" date" id="edit_blog_date" required>
                        </div>
                    </div>
                    <input type="submit" name="update_blog" value="Update Blog" class="reply-button">
                </form>
            </div>
        </div>
    </div>

    <script>
    function openEditModal(id, title, content, summary, author, date) {
        document.getElementById('editBlogModal').style.display = 'block';
        document.getElementById('edit_blog_id').value = id;
        document.getElementById('edit_blog_title').value = title;
        document.getElementById('edit_blog_content').value = content;
        document.getElementById('edit_blog_summary').value = summary;
        document.getElementById('edit_blog_author').value = author;
        document.getElementById('edit_blog_date').value = date;
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-XXXXX"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-XXXXX"
        crossorigin="anonymous"></script>
</body>

</html>