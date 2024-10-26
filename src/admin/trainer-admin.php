<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    require '../config/config.php';
}

// Handle trainer submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_trainer'])) {
    $full_name = $_POST['full_name'];
    $certification = $_POST['certification'];
    $specialties = $_POST['specialties'];
    $experience = $_POST['experience'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO trainers (full_name, certification, specialties, experience, description) 
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $full_name, $certification, $specialties, $experience, $description);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Trainer added successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to add trainer: " . $stmt->error;
    }

    header('Location: trainer-admin.php');
    exit;
}

// Handle trainer update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_trainer'])) {
    $id = $_POST['id'];
    $full_name = $_POST['full_name'];
    $certification = $_POST['certification'];
    $specialties = $_POST['specialties'];
    $experience = $_POST['experience'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("UPDATE trainers SET full_name = ?, certification = ?, specialties = ?, experience = ?, description = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $full_name, $certification, $specialties, $experience, $description, $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Trainer updated successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to update trainer: " . $stmt->error;
    }

    // Redirect to the same page
    header('Location: trainer-admin.php');
    exit;
}

// Handle trainer deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_trainer'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM trainers WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Trainer deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to delete trainer: " . $stmt->error;
    }
    header('Location: trainer-admin.php');
    exit;
}

// Fetch trainer data
$trainer_query = "SELECT id, full_name, certification, specialties, experience, description FROM trainers";
$trainer_result = $conn->query($trainer_query);

?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-XXXXX" crossorigin="anonymous">
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

    #addTrainerModal {
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

    #editTrainerModal {
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
        <a href="contactus-admin.php">Contact Us</a>
        <a class="active" href="trainer-admin.php">Trainers</a>
        <a href="blogs-admin.php">Blog</a>
    </div>

    <div class="content">
        <div class="container custom-container">
            <div class="header-section">
                <h3 style='font-weight:600'>Trainers</h3>
                <button onclick="document.getElementById('addTrainerModal').style.display='block'" class=" add-btn">Add
                    Trainer</button>
            </div>
        </div>

        <div class="container custom-container">
            <?php if (isset($_SESSION['message'])): ?>
            <div class="alert" style="background-color: #4CAF50;">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                <strong>Success!</strong> <?php echo htmlspecialchars($_SESSION['message']); ?>
            </div>
            <?php unset($_SESSION['message']);  ?>
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
                            <th>Full Name</th>
                            <th>Certification</th>
                            <th>Specialties</th>
                            <th>Experience</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($trainer_result->num_rows > 0) {
                            while ($trainer = $trainer_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td data-label='Full Name'>" . htmlspecialchars($trainer['full_name']) . "</td>";
                                echo "<td data-label='Certification'>" . htmlspecialchars($trainer['certification']) . "</td>";
                                echo "<td data-label='Specialties'>" . htmlspecialchars($trainer['specialties']) . "</td>";
                                echo "<td data-label='Experience'>" . htmlspecialchars($trainer['experience']) . "</td>";
                                echo "<td data-label='Description'>" . htmlspecialchars($trainer['description']) . "</td>";
                                echo "<td data-label='Action'>
                                    <div class='action-buttons'>
                                        <button class='edit-button' onclick='openEditModal(" . $trainer['id'] . ", \"" . addslashes($trainer['full_name']) . "\", \"" . addslashes($trainer['certification']) . "\", \"" . addslashes($trainer['specialties']) . "\", \"" . addslashes($trainer['experience']) . "\", \"" . addslashes($trainer['description']) . "\")'>Edit</button>
                                        <form method='POST' action='' style='display:inline;'>
                                            <input type='hidden' name='id' value='" . $trainer['id'] . "'>
                                            <button type='submit' name='delete_trainer' class='delete-button' onclick='return confirm(\"Are you sure you want to delete this trainer?\")'>Delete</button>
                                        </form>
                                    </div>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No trainers found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for adding trainer -->
    <div id="addTrainerModal" class="w3-modal">
        <div class="w3-modal-content w3-animate-top border-radius">
            <div class="w3-container">
                <span onclick="document.getElementById('addTrainerModal').style.display='none'"
                    class="w3-button w3-display-topright"
                    style="border-radius: 20px;margin-top:10px;margin-right:10px">&times;</span>
                <h3>Add Trainer</h3>
                <form method="POST" action="">
                    <div class="input-group">
                        <div class="input-item">
                            <label for="full_name">Full Name:</label>
                            <input type="text" id="full_name" name="full_name" required>
                        </div>
                        <div class="input-item">
                            <label for="experience">Experience:</label>
                            <input type="text" id="experience" name="experience" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-item">
                            <label for="certification">Certification:</label>
                            <input type="text" id="certification" name="certification" required>
                        </div>
                        <div class="input-item">
                            <label for="specialties">Specialties:</label>
                            <input type="text" id="specialties" name="specialties" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-item">
                            <label for="description">Description:</label>
                            <textarea id="description" name="description" rows="3" required></textarea>
                        </div>
                    </div>

                    <button type="submit" name="submit_trainer" class="reply-button">Add Trainer</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for editing trainer -->
    <div id="editTrainerModal" class="w3-modal">
        <div class="w3-modal-content w3-animate-top border-radius">
            <div class="w3-container">
                <span onclick="document.getElementById('editTrainerModal').style.display='none'"
                    class="w3-button w3-display-topright"
                    style="border-radius: 20px;margin-top:10px;margin-right:10px">&times;</span>
                <h3>Edit Trainer</h3>
                <form method="POST" action="">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="input-group">
                        <div class="input-item">
                            <label for="edit_full_name">Full Name:</label>
                            <input type="text" id="edit_full_name" name="full_name" required>
                        </div>
                        <div class="input-item">
                            <label for="edit_experience">Experience:</label>
                            <input type="text" id="edit_experience" name="experience" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-item">
                            <label for="edit_certification">Certification:</label>
                            <input type="text" id="edit_certification" name="certification" required>
                        </div>
                        <div class="input-item">
                            <label for="edit_specialties">Specialties:</label>
                            <input type="text" id="edit_specialties" name="specialties" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-item">
                            <label for="edit_description">Description:</label>
                            <textarea id="edit_description" name="description" rows="3" required></textarea>
                        </div>
                    </div>

                    <button type="submit" name="update_trainer" class="reply-button">Update Trainer</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function openEditModal(id, fullName, certification, specialties, experience, description) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_full_name').value = fullName;
        document.getElementById('edit_certification').value = certification;
        document.getElementById('edit_specialties').value = specialties;
        document.getElementById('edit_experience').value = experience;
        document.getElementById('edit_description').value = description;
        document.getElementById('editTrainerModal').style.display = 'block';
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-XXXXX"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-XXXXX"
        crossorigin="anonymous"></script>
</body>

</html>