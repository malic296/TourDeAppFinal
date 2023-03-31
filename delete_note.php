<?php
//DB connection
include "connection.php";

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM StickyNote WHERE ID = '$id'";
    if ($connection->query($sql) === TRUE) {
        echo "Note deleted successfully";
    } else {
        echo "Error deleting note: " . $connection->error;
    }
} else {
    echo "Error: ID not provided";
}
$connection->close();
?>