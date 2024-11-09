<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    require '../config/config.php';
}

// Fetch trainer data
$trainer_query = "SELECT id, full_name FROM trainers";
$trainer_result = $conn->query($trainer_query);

// Handle class submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_class'])) {
    $ClassName = $_POST['ClassName'];
    $Mode = $_POST['Mode'];
    $Description = $_POST['Description'];
    $Day = $_POST['Day'];
    $StartTime = $_POST['StartTime'];
    $EndTime = $_POST['EndTime'];
    $Price = $_POST['Price'];
    $TrainerID = $_POST['TrainerID'];

    $stmt = $conn->prepare("INSERT INTO class (ClassName, Mode, Description, Day, StartTime, EndTime,Price, TrainerID) VALUES (?, ?, ?,?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssi", $ClassName, $Mode, $Description, $Day, $StartTime, $EndTime, $Price, $TrainerID);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Class added successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to add Class: " . $stmt->error;
    }

    header('Location: class-admin.php');
    exit;
}

// Handle class update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_class'])) {
    $id = $_POST['id'];
    $ClassName = $_POST['ClassName'];
    $Mode = $_POST['Mode'];
    $Description = $_POST['Description'];
    $Day = $_POST['Day'];
    $StartTime = $_POST['StartTime'];
    $EndTime = $_POST['EndTime'];
    $Price = $_POST['Price'];
    $TrainerID = $_POST['TrainerID'];

    $stmt = $conn->prepare("UPDATE class SET ClassName = ?, Mode = ?, Description = ?, Day = ?, StartTime = ?, EndTime = ?,Price = ?, TrainerID = ? WHERE id = ?");
    $stmt->bind_param("sssssssii", $ClassName, $Mode, $Description, $Day, $StartTime, $EndTime,$Price, $TrainerID, $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Class updated successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to update class: " . $stmt->error;
    }

    header('Location: class-admin.php');
    exit;
}

// Handle class deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_class'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM class WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Class deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to delete class: " . $stmt->error;
    }

    header('Location: class-admin.php');
    exit;
}

// Fetch class data
$class_query = "SELECT class.*, trainers.full_name AS TrainerName 
                FROM class 
                JOIN trainers ON class.TrainerID = trainers.id";
$class_result = $conn->query($class_query);

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

    #addClassModal {
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

    #editClassModal {
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
            style='width:150px;height:100px; display:block; margin: 30px auto;border-radius:10%' alt="Logo">
        <a class="" href="membership-admin.php">Memberships</a>
        <a href="contactus-admin.php">Contact Us</a>
        <a href="trainer-admin.php">Trainers</a>
        <a class="active" href="class-admin.php">Classes</a>
        <a href="appointment-admin.php">Appointments</a>
        <a href="blogs-admin.php">Blog</a>
    </div>

    <div class="content">
        <div class="container custom-container">
            <div class="header-section">
                <h3 style='font-weight:600'>Classes</h3>
                <button onclick="document.getElementById('addClassModal').style.display='block'" class=" add-btn">Add
                    Class</button>
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
                            <th>Class Name</th>
                            <th>Mode</th>
                            <th>Description</th>
                            <th>Day</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Price</th>
                            <th>Trainer</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($class_result->num_rows > 0) {
                            while ($class = $class_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td data-label='Class Name'>" . htmlspecialchars($class['ClassName']) . "</td>";
                                echo "<td data-label='Mode'>" . htmlspecialchars($class['Mode']) . "</td>";
                                echo "<td data-label='Description'>" . htmlspecialchars($class['Description']) . "</td>";
                                echo "<td data-label='Day'>" . htmlspecialchars($class['Day']) . "</td>";
                                echo "<td data-label='Start Time'>" . htmlspecialchars($class['StartTime']) . "</td>";
                                echo "<td data-label='End Time'>" . htmlspecialchars($class['EndTime']) . "</td>";
                                echo "<td data-label='Price'>" . htmlspecialchars($class['Price']) . "</td>";
                                echo "<td data-label='Trainer'>" . htmlspecialchars($class['TrainerName']) . "</td>";
                                echo "<td data-label='Action'>
                                    <div class='action-buttons'>
                                        <button class='edit-button' onclick='openEditModal(" . json_encode($class) . ")'>Edit</button>
                                        <form method='POST' action='' onsubmit='return confirm(\"Are you sure you want to delete this class?\");' style='display:inline;'>
                                            <input type='hidden' name='id' value='" . $class['id'] . "'>
                                            <button type='submit' name='delete_class' class='delete-button'>Delete</button>
                                        </form>
                                    </div>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>No classes found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for adding trainer -->
    <div id="addClassModal" class="w3-modal">
        <div class="w3-modal-content w3-animate-top border-radius">
            <div class="w3-container">
                <span onclick="document.getElementById('addClassModal').style.display='none'"
                    class="w3-button w3-display-topright"
                    style="border-radius: 20px;margin-top:10px;margin-right:10px">&times;</span>
                <h3>Add Class</h3>
                <form method="POST" action="">
                    <div class="input-group">
                        <div class="input-item">
                            <label for="ClassName">Class Name:</label>
                            <input type="text" id="ClassName" name="ClassName" required>
                        </div>
                        <div class="input-item">
                            <label for="Mode">Mode:</label>
                            <select id="Mode" name="Mode" required>
                                <option value="Online">Online</option>
                                <option value="In-Person">In-Person</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-item">
                            <label for="Day">Day:</label>
                            <select id="Day" name="Day" required>
                                <option value="Sunday">Sunday</option>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                            </select>
                        </div>
                        <div class="input-item">
                            <label for="TrainerID">Trainer:</label>
                            <select id="TrainerID" name="TrainerID" required>
                                <?php
                                while ($trainer = $trainer_result->fetch_assoc()) {
                                    echo "<option value='" . $trainer['id'] . "'>" . htmlspecialchars($trainer['full_name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-item">
                            <label for="StartTime">Start Time:</label>
                            <input type="time" id="StartTime" name="StartTime" required>
                        </div>
                        <div class="input-item">
                            <label for="EndTime">End Time:</label>
                            <input type="time" id="EndTime" name="EndTime" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-item">
                            <label for="Price">Price:</label>
                            <input type="text" id="Price" name="Price" required pattern="^\d+(\.\d{1,2})?$"
                                title="Please enter a valid price (digits with up to two decimal places)">

                        </div>
                        <div class="input-item">
                            <label for="Description">Description:</label>
                            <textarea id="Description" name="Description" rows="3" required></textarea>
                        </div>
                    </div>

                    <button type="submit" name="submit_class" class="reply-button">Add Class</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for editing trainer -->
    <div id="editClassModal" class="w3-modal">
        <div class="w3-modal-content w3-animate-top border-radius">
            <div class="w3-container">
                <span onclick="document.getElementById('editClassModal').style.display='none'"
                    class="w3-button w3-display-topright"
                    style="border-radius: 20px; margin-top:10px; margin-right:10px">&times;</span>
                <h3>Edit Class</h3>
                <form method="POST" action="">
                    <input type="hidden" id="edit_class_id" name="id">

                    <div class="input-group">
                        <div class="input-item">
                            <label for="edit_ClassName">Class Name:</label>
                            <input type="text" id="edit_ClassName" name="ClassName" required>
                        </div>
                        <div class="input-item">
                            <label for="edit_Mode">Mode:</label>
                            <select id="edit_Mode" name="Mode" required>
                                <option value="Online">Online</option>
                                <option value="In-Person">In-Person</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-item">
                            <label for="edit_Day">Day:</label>
                            <select id="edit_Day" name="Day" required>
                                <option value="Sunday">Sunday</option>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                            </select>
                        </div>
                        <div class="input-item">
                            <label for="edit_TrainerID">Trainer:</label>
                            <select id="edit_TrainerID" name="TrainerID" required>
                                <?php
                            // Resetting the trainer result set if necessary and fetching trainer data for the dropdown.
                            $trainer_result->data_seek(0); // Reset pointer if needed
                            while ($trainer = $trainer_result->fetch_assoc()) {
                                echo "<option value='" . $trainer['id'] . "'>" . htmlspecialchars($trainer['full_name']) . "</option>";
                            }
                            ?>
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-item">
                            <label for="edit_StartTime">Start Time:</label>
                            <input type="time" id="edit_StartTime" name="StartTime" required>
                        </div>
                        <div class="input-item">
                            <label for="edit_EndTime">End Time:</label>
                            <input type="time" id="edit_EndTime" name="EndTime" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-item">
                            <label for="edit_Price">Price:</label>
                            <input type="text" id="edit_Price" name="Price" required pattern="^\d+(\.\d{1,2})?$"
                                title="Please enter a valid price (digits with up to two decimal places)">
                        </div>
                        <div class="input-item">
                            <label for="edit_Description">Description:</label>
                            <textarea id="edit_Description" name="Description" rows="3" required></textarea>
                        </div>
                    </div>

                    <button type="submit" name="update_class" class="reply-button">Update Class</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function openEditModal(classData) {
        document.getElementById('edit_class_id').value = classData.id;
        document.getElementById('edit_ClassName').value = classData.ClassName;
        document.getElementById('edit_Mode').value = classData.Mode;
        document.getElementById('edit_Description').value = classData.Description;
        document.getElementById('edit_Day').value = classData.Day;
        document.getElementById('edit_StartTime').value = classData.StartTime;
        document.getElementById('edit_EndTime').value = classData.EndTime;
        document.getElementById('edit_Price').value = classData.Price;
        document.getElementById('edit_TrainerID').value = classData.TrainerID;
        document.getElementById('editClassModal').style.display = 'block';
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-XXXXX"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-XXXXX"
        crossorigin="anonymous"></script>
</body>

</html>