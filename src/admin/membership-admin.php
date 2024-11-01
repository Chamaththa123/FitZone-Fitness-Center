<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    require '../config/config.php';
}

// Handle membership submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_membership'])) {
    $plan_name = $_POST['PlanName'];
    $description = $_POST['Description'];
    $price = $_POST['Price'];
    $duration = $_POST['Duration'];
    $benefits = $_POST['Benefits'];
    $special_promotions = $_POST['SpecialPromotions'];

    $stmt = $conn->prepare("INSERT INTO membership (PlanName, Description, Price, Duration, Benefits, SpecialPromotions) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $plan_name, $description, $price, $duration, $benefits, $special_promotions);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Membership plan added successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to add membership plan: " . $stmt->error;
    }

    header('Location: membership-admin.php');
    exit;
}

// Handle membership update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_membership'])) {
    $id = $_POST['id'];
    $plan_name = $_POST['PlanName'];
    $description = $_POST['Description'];
    $price = $_POST['Price'];
    $duration = $_POST['Duration'];
    $benefits = $_POST['Benefits'];
    $special_promotions = $_POST['SpecialPromotions'];

    $stmt = $conn->prepare("UPDATE membership SET PlanName = ?, Description = ?, Price = ?, Duration = ?, Benefits = ?, SpecialPromotions = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $plan_name, $description, $price, $duration, $benefits, $special_promotions, $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Membership plan updated successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to update membership plan: " . $stmt->error;
    }

    header('Location: membership-admin.php');
    exit;
}

// Handle membership deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_membership'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM membership WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Membership plan deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to delete membership plan: " . $stmt->error;
    }
    header('Location: membership-admin.php');
    exit;
}

// Fetch membership data
$membership_query = "SELECT id, PlanName, Description, Price, Duration, Benefits, SpecialPromotions FROM membership";
$membership_result = $conn->query($membership_query);

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

    #addMembershipModal {
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

    #editMembershipModal {
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
        <a class="active" class="" href="membership-admin.php">Memberships</a>
        <a href="contactus-admin.php">Contact Us</a>
        <a href="trainer-admin.php">Trainers</a>
        <a href="class-admin.php">Classes</a>
        <a href="blogs-admin.php">Blog</a>
    </div>

    <div class="content">
        <div class="container custom-container">
            <div class="header-section">
                <h3 style='font-weight:600'>MemberShips</h3>
                <button onclick="document.getElementById('addMembershipModal').style.display='block'"
                    class=" add-btn">Add
                    MemberShip</button>
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
                            <th>Plan Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th>Benefits</th>
                            <th>Special Promotions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($membership_result->num_rows > 0) {
                            while ($membership  = $membership_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td data-label='PlanName'>" . htmlspecialchars($membership['PlanName']) . "</td>";
                                echo "<td data-label='Description'>" . htmlspecialchars($membership['Description']) . "</td>";
                                echo "<td data-label='Price'>" . htmlspecialchars($membership['Price']) . "</td>";
                                echo "<td data-label='Duration'>" . htmlspecialchars($membership['Duration']) . "</td>";
                                echo "<td data-label='Benefits'>" . htmlspecialchars($membership['Benefits']) . "</td>";
                                echo "<td data-label='SpecialPromotions'>" . htmlspecialchars($membership['SpecialPromotions']) . "</td>";
                                echo "<td data-label='Action'>
                                    <div class='action-buttons'>
                                        <button class='edit-button' onclick='openEditModal(" . $membership['id'] . ", \"" . addslashes($membership['PlanName']) . "\", \"" . addslashes($membership['Description']) . "\", \"" . addslashes($membership['Price']) . "\", \"" . addslashes($membership['Duration']) . "\", \"" . addslashes($membership['Benefits']) . "\", \"" . addslashes($membership['SpecialPromotions']) . "\")'>Edit</button>
                                        <form method='POST' action='' style='display:inline;'>
                                            <input type='hidden' name='id' value='" . $membership['id'] . "'>
                                            <button type='submit' name='delete_membership' class='delete-button' onclick='return confirm(\"Are you sure you want to delete this trainer?\")'>Delete</button>
                                        </form>
                                    </div>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No memberships found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for adding trainer -->
    <div id="addMembershipModal" class="w3-modal">
        <div class="w3-modal-content w3-animate-top border-radius">
            <div class="w3-container">
                <span onclick="document.getElementById('addMembershipModal').style.display='none'"
                    class="w3-button w3-display-topright"
                    style="border-radius: 20px;margin-top:10px;margin-right:10px">&times;</span>
                <h3>Add Membership</h3>
                <form method="POST" action="">
                    <div class="input-group">
                        <div class="input-item">
                            <label for="PlanName">Plan Name:</label>
                            <input type="text" id="PlanName" name="PlanName" required>
                        </div>
                        <div class="input-item">
                            <label for="Duration">Duration:</label>
                            <input type="text" id="Duration" name="Duration" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-item">
                            <label for="Price">Price:</label>
                            <input type="text" id="Price" name="Price" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-item">
                            <label for="description">Description:</label>
                            <textarea id="Description" name="Description" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-item">
                            <label for="Benefits">Benefits:</label>
                            <textarea id="Benefits" name="Benefits" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-item">
                            <label for="SpecialPromotions">SpecialPromotions:</label>
                            <textarea id="SpecialPromotions" name="SpecialPromotions" rows="3" required></textarea>
                        </div>
                    </div>

                    <button type="submit" name="submit_membership" class="reply-button">Add Trainer</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for editing membership -->
    <div id="editMembershipModal" class="w3-modal">
        <div class="w3-modal-content w3-animate-top border-radius">
            <div class="w3-container">
                <span onclick="document.getElementById('editMembershipModal').style.display='none'"
                    class="w3-button w3-display-topright"
                    style="border-radius: 20px; margin-top:10px; margin-right:10px">&times;</span>
                <h3>Edit Membership</h3>
                <form method="POST" action="">
                    <input type="hidden" id="editId" name="id">
                    <div class="input-group">
                        <div class="input-item">
                            <label for="editPlanName">Plan Name:</label>
                            <input type="text" id="editPlanName" name="PlanName" required>
                        </div>
                        <div class="input-item">
                            <label for="editDuration">Duration:</label>
                            <input type="text" id="editDuration" name="Duration" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-item">
                            <label for="editPrice">Price:</label>
                            <input type="text" id="editPrice" name="Price" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-item">
                            <label for="editDescription">Description:</label>
                            <textarea id="editDescription" name="Description" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-item">
                            <label for="editBenefits">Benefits:</label>
                            <textarea id="editBenefits" name="Benefits" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-item">
                            <label for="editSpecialPromotions">Special Promotions:</label>
                            <textarea id="editSpecialPromotions" name="SpecialPromotions" rows="3" required></textarea>
                        </div>
                    </div>
                    <button type="submit" name="update_membership" class="reply-button">Update Membership</button>
                </form>
            </div>
        </div>
    </div>


    <script>
    function openEditModal(id, planName, description, price, duration, benefits, specialPromotions) {
        document.getElementById('editId').value = id;
        document.getElementById('editPlanName').value = planName;
        document.getElementById('editDescription').value = description;
        document.getElementById('editPrice').value = price;
        document.getElementById('editDuration').value = duration;
        document.getElementById('editBenefits').value = benefits;
        document.getElementById('editSpecialPromotions').value = specialPromotions;

        document.getElementById('editMembershipModal').style.display = 'block';
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-XXXXX"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-XXXXX"
        crossorigin="anonymous"></script>
</body>

</html>