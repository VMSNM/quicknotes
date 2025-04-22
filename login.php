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

                header("Location: dashboard.php");
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
    
    <!-- Preconnect to Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Google Fonts (Montserrat) -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <meta name="description" content="Take your notes anytime, anywhere and save them for later with the Online Notes App" />
    <meta name="author" content="Vitor Monteiro">
    <meta name="keywords" content="notes, notepad, quick notes, online notes" />

    <meta property="og:title" content="Quick Notes Online - Login">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.quick-notes.online/login.php">
    <meta property="og:description" content="Take your notes anytime, anywhere and save them for later with the Online Notes App">

    <title>Quick Notes Online - Login</title>
    
    <!-- Favicon & Fonts -->
    <link rel="icon" type="image/x-icon" href="images/quicknotesfavicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS & JS -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css">
    <script src="authpages_ads.js" defer></script>

    <!-- Google AdSense -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3756200931918777" crossorigin="anonymous"></script>
</head>
<body>
    <main id="main-auth">
        <div class="site-header">
            <div class="site-header-left">
                <a href="index.php">
                    <!-- <i class="ri-file-text-line"></i> --> 
                     <img src="images/quicknotes-logo-site.png" class="site-logo" alt="Quick Notes Logo" title="Quick Notes Logo" />
                    <h2>Quick Notes <span className="logo-dot">|</span></h2>
                </a>
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
                
                <button type="submit" id="loginBtn">
                    <span id="loginText">Login</span>
                    <span id="loginSpinner" class="spinner" style="display:none;"></span>
                </button>
            </form>
        </div>

        <!-- Mobile Google Ads Bottom -->
        <div class="mobile-ads-box mobile-ads-box-bottom">
            <div class="google-ad-wrapper google-ads2">
                <div class="ad-box-temp" style="position:absolute">Ad Zone</div>
                <!-- <div class="ad-loading-spinner"></div> -->
    
                <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3756200931918777"
                    crossorigin="anonymous">
                </script>

                <ins class="adsbygoogle"
                    style="display:inline-block;width:728px;height:90px"
                    data-ad-client="ca-pub-3756200931918777"
                    data-ad-slot="4584463441"></ins>

                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
        </div>

        <!-- Desktop Floating Google Ads Box -->
        <div class="floating-ad google-ad-wrapper" id="floatingAd">
            <div class="ad-box-temp" style="position:absolute">Ad Zone</div>
            <!-- <div class="ad-loading-spinner"></div> -->
            
            <span class="close-ad" onclick="hideFloatingAd()">×</span>

            <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3756200931918777"
                crossorigin="anonymous">
            </script>

            <ins class="adsbygoogle"
                style="display:inline-block;width:300px;height:250px"
                data-ad-client="ca-pub-3756200931918777"
                data-ad-slot="4584463441"></ins>

            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><i class="ri-copyright-line" style="font-size:16px !important"></i> Created by <a href="http://vmonteiro.netlify.app" class="copyright" target='_blank' rel='nofollow'>Vitor Monteiro</a> @ <a href="https://www.smart-center.pt" alt="Smart Informática em Espinho - Reparação de computadores e telemóveis, Criação de sites web" title="Smart Informática em Espinho - Reparação de computadores e telemóveis, Criação de sites web" target="_blank" rel="nofollow">Smart Informática</a></p>
            <div class="social">
                <a href="https://www.instagram.com/quicknotesonline" target="_blank" rel="nofollow" alt="Instagram Profile" title="Instagram Profile"><i class="ri-instagram-line"></i></a>
                <a href="https://x.com/quicknotesonlin" target="_blank" rel="nofollow" alt="Twitter Profile" title="Twitter Profile"><i class="ri-twitter-line"></i></a>
                <a href="http://www.linkedin.com/in/quick-notes-online" target="_blank" rel="nofollow" alt="Linkedin Profile" title="Linkedin Profile"><i class="ri-linkedin-fill"></i></i></a>
                <a href="mailto:support@quicknotesonline.com" alt="Email for support" title="Email for support"><i class="ri-mail-line"></i></a>
            </div>
        </div>
    </main>

    <script>
        const loginForm = document.querySelector("form");
        const loginBtn = document.getElementById("loginBtn");
        const loginText = document.getElementById("loginText");
        const loginSpinner = document.getElementById("loginSpinner");

        loginForm.addEventListener("submit", function () {
            // Disable button and show loading spinner
            loginBtn.disabled = true;
            loginBtn.style.opacity = "0.6";
            loginText.style.display = "none";
            loginSpinner.style.display = "inline-block";
        });
    </script>

    <script>
        const usernameInput = document.querySelector('input[name="username"]');
        const passwordInput = document.querySelector('input[name="password"]');
        const errorMessage = document.querySelector('form p[style*="color:red"]');

        function clearErrorMessage() {
            if (errorMessage && errorMessage.style.display !== "none") {
                errorMessage.style.opacity = "0";
                setTimeout(() => {
                    errorMessage.style.display = "none";
                }, 300);
            }
        }

        usernameInput.addEventListener("input", clearErrorMessage);
        passwordInput.addEventListener("input", clearErrorMessage);
    </script>
    <!-- <script src="code.js"></script> -->
</body>
</html>