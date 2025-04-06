<?php
    include("config.php");
    if(!isset($_SESSION)) { session_start(); }

    if (!isset($_SESSION["user_id"])) {
        header("Location: public_notes.php");
        exit();
    }

    $user_id = $_SESSION["user_id"];

    // Fetch the username from the database
    /* $stmt = $conn->prepare("SELECT username, logged_times, last_login FROM users WHERE id = ?"); */
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    /* $stmt->bind_result($username, $logged_times, $last_login); */
    $stmt->bind_result($username);
    $stmt->fetch();
    $stmt->close();

    $result = $conn->query("SELECT id, note FROM notes WHERE user_id = " . $_SESSION["user_id"]);
    $notes = [];
    while ($row = $result->fetch_assoc()) {
        $notes[] = $row;
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

    <meta property="og:title" content="Quick Notes Online - Dashboard">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.quick-notes.online">
    <meta property="og:description" content="Take your notes anytime, anywhere and save them for later with the Online Notes App">

    <title>Quick Notes Online - Dashboard</title>
    <link rel="icon" type="image/svg+xml" href="images/smartfavicon.ico" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="index.js" defer></script> <!-- External JavaScript file -->
</head>
<body>
    <main>
        <div class="modal" id="modal">
            <div class="modal-overlay" id="modal-overlay"></div>
            <div class="modal-content" id="modal-content">
            </div>
        </div>

        <div class="toast" id="toast">
            <div class="toast-content" id="toast-content">
            </div>
        </div>

        <!-- Header -->
        <div class="site-header">
            <div class="site-header-left">
                <h2><a href="index.php"><i class="ri-file-text-line"></i> Quick Notes <span className="logo-dot">|</span></a></h2>
                <h3 class="username">Welcome <?php echo htmlspecialchars($username); ?></h3>
            </div>

            <!-- Desktop Menu -->
            <div class="site-header-right dashboard-header-right">
                <i class="ri-save-3-line action-btn save-to-db" onclick="saveNotesToDatabase();" title='Save to Database'></i>
                <i class="ri-file-download-line action-btn" onclick="downloadToLocalFile();" title='Download as Excel File'></i>
                <i class="ri-screenshot-2-line action-btn" onclick="downloadAsPicture();" title='Download as Picture'></i>
                <i class="ri-logout-box-line action-btn" onclick="openModal('logout');" title='Logout'></i>
            </div>

            <!-- Mobile Menu -->
            <div class="mobile-menu">
                <i class="ri-menu-line menu-btn" id="menu-btn" onclick="" title=''></i>

                <div class="mobile-menu-items" id="mobile-menu-items">
                    <div class="mobile-item" onclick="saveNotesToDatabase();" title='Save to Database'>
                        <i class="ri-save-3-line action-btn"></i>
                        <p>Save to Database</p>
                    </div>

                    <div class="mobile-item" onclick="downloadToLocalFile();" title='Download as Excel File'>
                        <i class="ri-file-download-line action-btn"></i>
                        <p>Download as Excel File</p>
                    </div>

                    <div class="mobile-item" onclick="downloadAsPicture();" title='Download as Picture'>
                        <i class="ri-screenshot-2-line action-btn"></i>
                        <p>Download as Picture</p>
                    </div>
                    
                    <div class="mobile-item" onclick="openModal('logout');" title='Logout'>
                        <i class="ri-logout-box-line action-btn"></i>
                        <p>Logout</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Color picker -->
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

    <script>
        let notesData = <?php echo json_encode($notes); ?>;
        document.addEventListener("DOMContentLoaded", function() { if (notesData[0]?.notes !== '') loadNoteFromDashboard(notesData[0]); });
    </script>
</body>
</html>
