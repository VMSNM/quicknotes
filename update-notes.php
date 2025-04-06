<?php
include("config.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION["user_id"])) {
        echo "User not logged in";
        exit();
    }

    $note_id = $_POST["id"];
    $new_note = $_POST["note"];

    $stmt = $conn->prepare("UPDATE notes SET note = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $new_note, $note_id, $_SESSION["user_id"]);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error updating note.";
    }

    $stmt->close();
}
?>
