<?php 

$server = "localhost";
$username = "root";
$password = "";
$database = "todo_master";

// Establishing a connection to the database
$connection = mysqli_connect($server, $username, $password, $database);

// Check if the connection was successful
if (!$connection) {
    die("Connection to MySQL failed: " . mysqli_connect_error());
}

// Initialize variables for success messages
$successMessage = '';

// Handling the creation of a TO DO item
if (isset($_POST['add'])) {
    $item = $_POST['item'];
    if (!empty($item)) {
        $query = "INSERT INTO todo (name, status) VALUES ('$item', 0)"; // Added default value for 'status'
        if (mysqli_query($connection, $query)) {
            $successMessage = 'Item added successfully...!';
        } else {
            echo mysqli_error($connection);
        }
    }
}

// Handling actions like marking an item as done or deleting an item
if (isset($_GET['action'])) {
    $itemId = $_GET['item'];
    if ($_GET['action'] == 'done') {
        $query = "UPDATE todo SET status = 1 WHERE id='$itemId'";
        if (mysqli_query($connection, $query)) {
            $successMessage = 'Item marked as Successfully...!';
        } else {
            echo mysqli_error($connection);
        }
    } elseif ($_GET['action'] == 'delete') {
        $query = "DELETE FROM todo WHERE id='$itemId'";
        if (mysqli_query($connection, $query)) {
            $successMessage = 'Item deleted Successfully...!';
        } else {
            echo mysqli_error($connection);
        }
    }
}

// Displaying the items from the database
$query = "SELECT * FROM todo";
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> <!-- Include jQuery -->
    <style>
        body {
            background-image: url('https://png.pngtree.com/thumb_back/fh260/background/20210814/pngtree-blue-purple-simple-gradient-background-image_760572.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            margin: 0;
            padding: 0;
            height: 100vh; /* Set full height of viewport */
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: flex-start; /* Align container to top */
        }
        .container {
            position: relative; /* Make the container a positioning context */
            width: 500px;
            margin-top: 100px; /* Adjust top margin for spacing */
            padding: 20px;
            border-radius: 10px;
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .alert {
            position: absolute; /* Position the alert absolutely */
            top: 10px; /* Adjust top position as needed */
            left: 50%; /* Center the alert horizontally */
            transform: translateX(-50%); /* Center the alert horizontally */
            z-index: 9999; /* Ensure the alert appears above other content */
            border-radius: 10px;
            margin-top: 20px;
        }
        .item {
            margin-bottom: 10px; /* Add margin bottom to create space between item boxes */
        }
        .card {
            background-image: linear-gradient(to left,#ffa31a,#33ff33);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px; /* Add margin bottom to create space between item boxes */

        }
        .done {
            text-decoration: line-through;
        }
        .btn-outline-dark,
        .btn-outline-danger {
            border-radius: 20px;
            margin-right: 10px;
        }
        .btn-outline-dark:hover,
        .btn-outline-danger:hover {
            opacity: 0.8;
        }
        h5 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php if (!empty($successMessage)) { ?>
    <div class="alert alert-success success-message" id="success-message"><?php echo $successMessage; ?></div>
    <?php } ?>
    <div class="container">
        <h2 class="text-center">To-Do List</h2>
        <form id="todo-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="form-group">
                <input type="text" id="new-item" name="item" class="form-control" placeholder="Enter new item">
            </div>
            <button type="submit" class="btn btn-primary btn-block" name="add">Add to List</button>
        </form>
        <div class="mt-3">
            <h3 class="text-center">List of Items</h3>
            <div id="todo-list">
                <?php
                // Check if there are no rows in the result set
                if (mysqli_num_rows($result) > 0) {
                    // Loop through each row in the result set
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Output each item as a list item
                        echo '<div class="item card">';
                        echo '<div class="card-body">';
                        // Apply "done" class if item status is 1 (read)
                        $itemClass = ($row['status'] == 1) ? 'done' : '';
                        echo '<span class="' . $itemClass . '">' . htmlspecialchars($row['name']) . '</span>';
                        echo '<div class="item-buttons mt-2">';
                        echo '<a href="?action=delete&item=' . $row['id'] . '" class="btn btn-danger delete-btn">Delete</a>';
                        echo '&nbsp;'; // Inserting a non-breaking space for spacing
                        echo '<a href="?action=done&item=' . $row['id'] . '" class="btn btn-success mark-btn">Mark as Read</a>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<center>';
                    echo '<img src="file.png" width="75px" alt="Empty List"><br>';
                    echo '<span>Your List is Empty</span>';
                    echo '</center>';
                }
                ?>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            $(".alert").fadeTo(5000,500).slideUp(500,function(){
                $('.alert').slideUp(300);
            });
        });
    </script>
</body>
</html>
