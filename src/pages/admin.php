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
        margin: 3px 10px;
        border-radius: 5px;
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
    </style>
</head>

<body>

    <div class="sidebar">
        <img src="public/assets/images/StockCake-Gym Running Workout_1729507439.jpg" class='logo' style='width:80px'
            alt="Logo">
        <a class="" href="#home">Home</a>
        <a href="#news">News</a>
        <a href="#contact">Contact</a>
        <a class="active" href="#about">About</a>
    </div>

    <div class="content">
        <div class="container custom-container">
            <div class="header-section">
                <h2>Header</h2>
                <button onclick="document.getElementById('id01').style.display='block'" class="w3-button w3-black">Open
                    Modal</button>
            </div>

        </div>

        <div class="container custom-container">
            table here

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