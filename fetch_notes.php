<?php
include("config.php");
session_start();

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$result = $conn->query("SELECT id, note FROM notes WHERE user_id = " . $_SESSION["user_id"]);
$notes = [];

while ($row = $result->fetch_assoc()) {
    $notes[] = $row;
}

echo json_encode($notes); // Send data as JSON
?>
