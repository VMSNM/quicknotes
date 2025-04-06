<?php
    include("config.php");

    if (isset($_SESSION["user_id"])) {
        header("Location: index.php");
        exit();
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

    <meta property="og:title" content="Quick Notes Online">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.quick-notes.online/public_notes.php">
    <meta property="og:description" content="Take your notes anytime, anywhere and save them for later with the Online Notes App">

    <title>Quick Notes Online</title>
    <link rel="icon" type="image/svg+xml" href="images/smartfavicon.ico" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css">

    <script src="public_notes.js" defer></script>
</head>
<body>
    <main>

        <!-- Modal -->
        <div class="modal" id="modal">
            <div class="modal-overlay" id="modal-overlay"></div>
            <div class="modal-content">
                <i class="ri-close-circle-line modal-close" id="modal-close"></i>
                <p>Sure you want to delete this note?</p>
                <div class="modal-btns">
                    <button class="modal-btn modal-btn-confirm" id="modal-btn-confirm">Confirm</button>
                    <button class="modal-btn modal-btn-cancel" id="modal-btn-cancel">Cancel</button>
                </div>
            </div>
        </div>

        <!-- Header -->
        <div class="site-header">
            <div class="site-header-left">
                <h2><a href="index.php"><i class="ri-file-text-line"></i> Quick Notes <span className="logo-dot">|</span></a></h2>
            </div>
            <div class="site-header-right">
                <a href="login.php" title="Login to save your notes" class="action-btn">Login</a>
            </div>
        </div>
        

        <!-- Color Picker -->
        <form action="" id="color-chooser">
            <input type="color" id="color" value="#4cc11a">
            <button type="button" id="createBtn"><i class="ri-add-line"></i></button>
        </form>

        <!-- Google Ads -->
        <div class="ads-box">
            <div class="google-ads1">
                <a href="#"><img src="images/banner.jpg" /></a>
            </div>
        </div>

        <!-- Notes List to Populate-->
        <div id="list"></div>

        <!-- Google Ads -->
        <div class="ads-box">
            <div class="google-ads2">
                <a href="#"><img src="images/banner.jpg" /></a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Created by <a href="http://vmonteiro.netlify.app" class="copyright" target='_blank'>Vitor Monteiro</a> <i class="ri-copyright-line" style="font-size:16px !important"></i> For support <a href="mailto:vitor.monteiro.84@gmail.com">email us</a></p>
        </div>
    </main>
</body>
</html>