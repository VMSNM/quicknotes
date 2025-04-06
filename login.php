<?php
    include("config.php");

    if (isset($_SESSION["user_id"])) {
        header("Location: index.php");
        exit();
    }
    
    $error_message = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $password = $_POST["password"];
        
        $stmt = $conn->prepare("SELECT id, username, password, logged_times FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $username, $hashed_password, $logged_times);
            $stmt->fetch();
            
            if (password_verify($password, $hashed_password)) {
                session_start();
                $_SESSION["user_id"] = $user_id;
                $_SESSION["username"] = $username;
                
                // Increment logged_times and update last_login
                $logged_times++;
                $current_time = date("Y-m-d H:i:s");

                $update_stmt = $conn->prepare("UPDATE users SET logged_times = ?, last_login = ? WHERE id = ?");
                $update_stmt->bind_param("isi", $logged_times, $current_time, $user_id);
                $update_stmt->execute();
                $update_stmt->close();

                header("Location: index.php");
                exit();
            } else {
                $error_message = "Invalid username or password.";
            }
        } else {
            $error_message = "Invalid username or password.";
        }
        
        $stmt->close();
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

    <meta property="og:title" content="Quick Notes Online - Login">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.quick-notes.online/login.php">
    <meta property="og:description" content="Take your notes anytime, anywhere and save them for later with the Online Notes App">

    <title>Quick Notes Online - Login</title>
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
                <p><a href="signup.php" class="action-btn">Signup</a></p>
            </div>
        </div>

        <div class="auth-form">
            <h2>Login</h2>
            <form method="POST">
                <input type="text" name="username" required placeholder="Username">
                <input type="password" name="password" required placeholder="Password">
                <?php if (!empty($error_message)) { echo "<p style='color:red;margin:10px 0;text-align:center'>$error_message</p>"; } ?>
                <button type="submit">Login</button>
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