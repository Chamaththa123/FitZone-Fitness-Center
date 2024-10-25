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

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .reply-button,
        .edit-button,
        .delete-button {
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            /* Increased padding */
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 16px;
            /* Consistent font size */
        }

        .reply-button {
            background-color: #000000;
            color: white;
        }

        .edit-button {
            background-color: #007bff;
            color: white;
        }

        .delete-button {
            background-color: #dc3545;
            color: white;
        }

        .reply-button:hover {
            background-color: #121112;
        }

        .edit-button:hover {
            background-color: #0056b3;
        }

        .delete-button:hover {
            background-color: #c82333;
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


        .alert.success {
            background-color: #4CAF50;
            /* Green for success */
        }

        .alert.error {
            background-color: #f44336;
            /* Red for error */
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

        .modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0, 0, 0); /* Fallback color */
    background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
}

.modal-content {
    background-color: #fefefe;
    margin: 10% 20% 0 auto; /* Adjust the margins */
    padding: 20px;
    border: 1px solid #888;
    border-radius: 8px; /* Rounded corners */
    width: calc(80% - 40px); /* Set width to 80% minus padding */
    max-width: 800px; /* Optional: maximum width */
    position: fixed; /* Fixed position to keep it in view */
    right: 0; /* Align it to the right */
}

.modal-content input,
.modal-content textarea {
    width: 100%; /* Full width */
    padding: 10px; /* Inner padding */
    margin-bottom: 10px; /* Space between inputs */
    border: 1px solid #ced4da; /* Border color */
    border-radius: 4px; /* Rounded borders */
    font-size: 16px; /* Font size for inputs */
    transition: border-color 0.3s ease; /* Smooth border color transition */
}

.modal-content input:focus,
.modal-content textarea:focus {
    border-color: #007bff; /* Highlight border on focus */
    outline: none; /* Remove default outline */
}

.modal-content label {
    font-weight: 600; /* Bold font for labels */
    margin-bottom: 5px; /* Space below labels */
}

    </style>
</head>

<body>

    <div class="sidebar">
        <img src="../../public/assets/images/logo.jpg" class='logo' style='width:150px; display:block; margin: 30px auto;' alt="Logo">
        <a href="../../admin.php">Home</a>
        <a href="#news">News</a>
        <a href="#contact">Contact</a>
        <a href="contactus-admin.php">Contact Us</a>
        <a class="active" href="blogs-admin.php">Blogs</a>
    </div>

    <div class="content">
        <div class="container custom-container">
            <div class="header-section">
                <h3 style='font-weight:600'>Blogs</h3>
                <button onclick="document.getElementById('addBlogModal').style.display='block'" class="reply-button">Add Blog</button>
            </div>
        </div>

        <div class="container custom-container">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert success">
                    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                    <strong>Success!</strong> <?php echo htmlspecialchars($_SESSION['message']); ?>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert error">
                    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                    <strong>Error!</strong> <?php echo htmlspecialchars($_SESSION['error_message']); ?>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <div class="table-container">
                <table class="table table-bordered">
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
    <div id="addBlogModal" class="modal">
        <div class="modal-content">
            <span onclick="document.getElementById('addBlogModal').style.display='none'" class="close">&times;</span>
            <h2>Add Blog</h2>
            <form method="POST" action="">
                <label for="title">Title:</label>
                <input type="text" name="title" required>
                <label for="content">Content:</label>
                <textarea name="content" required></textarea>
                <label for="summary">Summary:</label>
                <textarea name="summary" required></textarea>
                <label for="author">Author:</label>
                <input type="text" name="author" required>
                <label for="date">Date:</label>
                <input type="date" name="date" required>
                <input type="submit" name="submit_blog" value="Add Blog" class="reply-button">
            </form>
        </div>
    </div>

    <!-- Edit Blog Modal -->
    <div id="editBlogModal" class="modal">
        <div class="modal-content">
            <span onclick="document.getElementById('editBlogModal').style.display='none'" class="close">&times;</span>
            <h2>Edit Blog</h2>
            <form method="POST" action="">
                <input type="hidden" name="id" id="edit_blog_id">
                <label for="title">Title:</label>
                <input type="text" name="title" id="edit_blog_title" required>
                <label for="content">Content:</label>
                <textarea name="content" id="edit_blog_content" required></textarea>
                <label for="summary">Summary:</label>
                <textarea name="summary" id="edit_blog_summary" required></textarea>
                <label for="author">Author:</label>
                <input type="text" name="author" id="edit_blog_author" required>
                <label for="date">Date:</label>
                <input type="date" name="date" id="edit_blog_date" required>
                <input type="submit" name="update_blog" value="Update Blog" class="edit-button">
            </form>
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

</body>

</html>