<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
if (!isset($conn)) {
    require '../config/config.php';  // Database connection
}

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_reply'])) {
    $reply = $_POST['reply'];
    $contact_id = $_POST['contact_id'];

    // Update reply in the contact_us table
    $stmt = $conn->prepare("UPDATE contact_us SET reply = ? WHERE id = ?");
    $stmt->bind_param("si", $reply, $contact_id);

    if ($stmt->execute()) {
        $success_message = "Reply sent successfully!";
    } else {
        $error_message = "Failed to send reply: " . $stmt->error;
    }
}

// Fetch contact us data
$query = "SELECT c.id, c.subject, c.message, c.reply, u.first_name, u.last_name 
          FROM contact_us c 
          JOIN users u ON c.customer_id = u.id
          ORDER BY c.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-XXXXX" crossorigin="anonymous">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

    body {
        margin: 0;
        font-family: 'Poppins', Arial, Helvetica, sans-serif;
    }

    .sidebar {
        margin: 0;
        padding: 0;
        width: 200px;
        background-color: #10173c;
        position: fixed;
        height: 100%;
        overflow: auto;
        border-top-right-radius: 40px;
        border-bottom-right-radius: 40px;
    }

    .sidebar a {
        display: block;
        color: white;
        padding: 16px;
        text-decoration: none;
    }

    .sidebar a.active {
        background-color: white;
        color: #d91f26;
        font-weight: bold;
    }

    .sidebar a:hover:not(.active) {
        background-color: white;
        color: #555;
    }

    div.content {
        margin-left: 150px;
        padding: 10px 15px 0px 66px;
        min-height: 100vh;
        background-color: #f5f5f5;
    }

    .custom-container {
        background-color: white;
        padding: 5px 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }

    .header-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    @media screen and (max-width: 700px) {
        .sidebar {
            width: 100%;
            height: auto;
            position: relative;
        }

        .sidebar a {
            float: left;
        }

        div.content {
            margin-left: 0;
        }
    }

    @media screen and (max-width: 400px) {
        .sidebar a {
            text-align: center;
            float: none;
        }
    }

    .border-radius {
        border-radius: 20px;
        padding: 40px 20px
    }

    .input-group {
        display: flex;
        gap: 20px;
        margin-bottom: 15px;
    }

    .input-item {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    /* Responsive layout */
    @media (max-width: 600px) {
        .input-group {
            flex-direction: column;
        }
    }

    input[type=text],
    input[type=password] {
        width: 100%;
        padding: 12px 20px;
        margin: 8px 0;
        display: inline-block;
        border: 1px solid #ccc;
        box-sizing: border-box;
    }

    .table-container {
        max-width: 100%;
        overflow-x: auto;
        margin: 20px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    thead {
        background-color: #d0d0d0;
        color: #4d4b4b;
    }

    th,
    td {
        padding: 12px 15px;
        text-align: left;
        border: 1px solid #ddd;
        font-size: 13px
    }

    th {
        font-weight: bold;
    }

    .reply {
        border-radius: 10px;
        padding: 10px;
        border: 1px solid #ccc;
        font-size: 14px;
        width: 100%;
        resize: vertical;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .reply-button {
        background-color: #00af00;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 5px 7px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-size: 12px;
    }


    wid @media screen and (max-width: 768px) {

        table,
        th,
        td {
            display: block;
        }

        th {
            display: none;
        }

        td {
            padding: 10px;
            text-align: right;
            position: relative;
        }

        td::before {
            content: attr(data-label);
            position: absolute;
            left: 0;
            width: 50%;
            padding-left: 15px;
            font-weight: bold;
            text-align: left;
        }
    }
    </style>
</head>

<body>

    <div class="sidebar">
        <img src="../../public/assets/images/logo.jpg" class='logo'
            style='width:150px; display:block; margin: 30px auto;' alt="Logo">

        <a class="" href="admin.php">Home</a>
        <a class='active' ref="contactus-admin.php">Contact Us</a>
        <a href="trainer-admin.php">Trainers</a>
        <a href="class-admin.php">Classes</a>
        <a href="blogs-admin.php">Blog</a>
    </div>

    <div class="content">
        <div class="container custom-container">
            <div class="header-section">
                <h3 style='font-weight:600'>Contact Us</h3>
                <button onclick="document.getElementById('id01').style.display='block'" class="w3-button w3-black">Open
                    Modal</button>
            </div>

        </div>

        <div class="container custom-container">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Reply</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td data-label='Customer'>" . htmlspecialchars($row['first_name'] ?? '') . " " . htmlspecialchars($row['last_name'] ?? '') . "</td>";
                                    echo "<td data-label='Subject'>" . htmlspecialchars($row['subject'] ?? '') . "</td>";
                                    echo "<td data-label='Message'>" . htmlspecialchars($row['message'] ?? '') . "</td>";
                                    echo "<td data-label='Reply'>" . htmlspecialchars($row['reply'] ?? 'No reply yet') . "</td>";
                                    echo "<td>";
                                    echo "<form method='POST' action='" . $_SERVER['PHP_SELF'] . "'>";
                                    echo "<textarea name='reply' placeholder='Enter your reply' class='reply' required>" . htmlspecialchars($row['reply'] ?? '') . "</textarea>";
                                    echo "<input type='hidden' name='contact_id' value='" . $row['id'] . "' />";
                                    echo "<button type='submit' name='submit_reply' class='reply-button'>Send Reply</button>";
                                    echo "</form>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No messages found.</td></tr>";
                            }
?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>



    <!-- The Modal -->
    <div id="id01" class="w3-modal">
        <div class="w3-modal-content border-radius">
            <div class="w3-container">
                <span onclick="document.getElementById('id01').style.display='none'"
                    class="w3-button w3-display-topright"
                    style="border-radius: 20px;margin-top:10px;margin-right:10px">&times;</span>


                <form class="modal-form">
                    <div class="input-group">
                        <div class="input-item">
                            <label for="input1">Input 1:</label>
                            <input type="text" id="input1" name="input1">
                        </div>
                        <div class="input-item">
                            <label for="input2">Input 2:</label>
                            <input type="text" id="input2" name="input2">
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-item">
                            <label for="input3">Input 3:</label>
                            <input type="text" id="input3" name="input3">
                        </div>
                        <div class="input-item">
                            <label for="input4">Input 4:</label>
                            <input type="text" id="input4" name="input4">
                        </div>
                    </div>

                    <button type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-XXXXX"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-XXXXX"
        crossorigin="anonymous"></script>
</body>

</html>