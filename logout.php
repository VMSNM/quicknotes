<?php
    session_start();
    session_destroy();
    header("Location: public_notes.php");
?>