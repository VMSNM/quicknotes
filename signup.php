<?php
    include("config.php");

    if (isset($_SESSION["user_id"])) {
        header("Location: index.php");
        exit();
    }

    $error_message = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        
        // Check if the username already exists
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $error_message = "Username already exists. Please choose a different one.";
        } else {
            // Insert new user into the database
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $password);
            
            if ($stmt->execute()) {
                $user_id = $conn->insert_id; // Get the ID of the newly created user
                
                // Default note content
                $default_note = '[{"id":1,"color":"#4cc11a","noteContent":"Note 1 alterada","x":242,"y":325}]';
                
                // Automatically create a note with default content for the new user
                $stmt_note = $conn->prepare("INSERT INTO notes (user_id, note) VALUES (?, ?)");
                $stmt_note->bind_param("is", $user_id, $default_note);
                $stmt_note->execute();
                $stmt_note->close();
                
                header("Location: login.php");
                exit();
            } else {
                $error_message = "Error: " . $stmt->error;
            }
            
            $stmt->close();
        }
        
        $check_stmt->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <meta name="description" content="Take your notes anytime, anywhere and save them for later with the Online Notes App" />
    <meta name="author" content="Vitor Monteiro">
    <meta name="keywords" content="notes, notepad, quick notes, online notes" />

    <meta property="og:title" content="Quick Notes Online - Signup">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.quick-notes.online/signup.php">
    <meta property="og:description" content="Take your notes anytime, anywhere and save them for later with the Online Notes App">

    <title>Quick Notes Online - Signup</title>
    <link rel="icon" type="image/svg+xml" href="images/smartfavicon.ico" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main id="main-auth">
        <div class="site-header">
            <div class="site-header-left">
                <h2><a href="index.php"><i class="ri-file-text-line"></i> Quick Notes <span className="logo-dot">|</span></a></h2>
            </div>
            <div class="site-header-right">
                <p><a href="login.php" class="action-btn">Login</a></p>
            </div>
        </div>
        <div class="auth-form">
            <h2>Signup</h2>
            <form method="POST">
                <input type="text" name="username" required placeholder="Username">
                <input type="password" name="password" required placeholder="Password">
                <?php if (!empty($error_message)) { echo "<p style='color:red;margin:10px 0;text-align:center'>$error_message</p>"; } ?>
                <button type="submit">Sign Up</button>
            </form>
        </div>

        <div class="ads-box">
            <div class="google-ads2">
                <a href="#"><img src="images/banner.jpg" /></a>
            </div>
        </div>

        <div class="footer">
            <p>Created by <a href="http://vmonteiro.netlify.app" class="copyright" target='_blank'>Vitor Monteiro</a> <i class="ri-copyright-line" style="font-size:16px !important"></i> For support <a href="mailto:vitor.monteiro.84@gmail.com">email us</a></p>
        </div>
    </main>

    <!-- <script src="code.js"></script> -->
</body>
</html>