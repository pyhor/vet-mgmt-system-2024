<?php
// Include database connection
include 'connect_to_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $field = $_POST['field'];
    $value = $_POST['value'];

    // If the value is empty, return a specific response
    if (empty($value)) {
        echo json_encode(['status' => 'empty']);
        exit; // Stop further execution
    }

    // Example checks (you should replace this with actual database queries)
    $exists = false;

    if ($field === 'vetId') {
        // Check if vetId exists in the database
        $query = "SELECT vetid FROM vet WHERE vetid = ?";
    } elseif ($field === 'username') {
        // Check if username exists
        $query = "SELECT username FROM vet WHERE username = ?";
    } elseif ($field === 'email') {
        // Check if email exists
        $query = "SELECT email FROM vet WHERE email = ?";
    } elseif ($field === 'phonenum') {
        // Check if phone number exists
        $query = "SELECT phonenum FROM vet WHERE phonenum = ?";
    }

    if ($stmt = mysqli_prepare($connect, $query)) {
        mysqli_stmt_bind_param($stmt, 's', $value);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $exists = true;
        }

        mysqli_stmt_close($stmt);
    }

    // Return response
    if ($exists) {
        echo json_encode(['status' => 'exists']);
    } else {
        echo json_encode(['status' => 'available']);
    }
}
?>