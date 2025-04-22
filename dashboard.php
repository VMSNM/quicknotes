<?php
    include("config.php");
    if(!isset($_SESSION)) { session_start(); }

    if (!isset($_SESSION["user_id"])) {
        header("Location: index.php", true, 301);
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

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-00M23XHJ43"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-00M23XHJ43');
    </script>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "Quick Notes Online",
        "url": "https://quicknotesonline.com/dashboard.php",
        "logo": "https://quicknotesonline.com/images/quicknotes-logo.png",
        "sameAs": [
            "https://www.instagram.com/quicknotesonline",
            "https://x.com/quicknotesonlin",
            "https://www.linkedin.com/in/quick-notes-online"
        ],
        "description": "Take your notes anytime, anywhere and save them for later with the Online Notes App - User Dashboard",
        "founder": "Vitor Monteiro",
        "foundingDate": "2025-04-15",
        "email": "support@quicknotesonline.com",
        "contactPoint": {
            "@type": "ContactPoint",
            "email": "support@quicknotesonline.com",
            "contactType": "customer support",
            "availableLanguage": ["English", "Portuguese"]
        }
    }
    </script>
  
  <title>Quick Notes Online - Take Your Notes Anytime, Anywhere</title>

  <meta name="description" content="Save your notes instantly with Quick Notes Online. Our simple, easy-to-use online notepad allows you to take and store your notes securely and access them anytime, anywhere." />
  <meta name="keywords" content="online notes, free notepad, quick notes, take notes online, simple notes app" />

  <meta name="author" content="Vitor Monteiro">

  <!-- Open Graph / Social -->
  <!-- Open Graph Meta Tags -->
  <meta property="og:title" content="Quick Notes Online - Take Your Notes Anytime, Anywhere" />
  <meta property="og:description" content="Save your notes instantly with Quick Notes Online. Our simple, easy-to-use online notepad allows you to take and store your notes securely and access them anytime, anywhere." />
  <meta property="og:url" content="https://quicknotesonline.com/dashboard.php" />
  <meta property="og:type" content="website" />
  <!-- <meta property="og:image" content="https://quicknotesonline.com/images/preview_image.jpg" /> -->

  <!-- Canonical Link -->
  <link rel="canonical" href="https://quicknotesonline.com/dashboard.php">

  <!-- Favicon & Fonts -->
  <link rel="icon" type="image/x-icon" href="images/quicknotesfavicon.ico">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- CSS & JS -->
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css">
  <script src="dashboard.js" defer></script>

  <!-- Google AdSense -->
  <!-- <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3756200931918777" crossorigin="anonymous"></script> -->
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
                <a href="index.php">
                    <!-- <i class="ri-file-text-line"></i> --> 
                     <img src="images/quicknotes-logo-site.png" class="site-logo" alt="Quick Notes Logo" title="Quick Notes Logo" />
                    <h2>Quick Notes <span className="logo-dot">|</span></h2>
                </a>
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
            <input type="color" id="color" value="#4cc11a" alt="Pick note color" title="Pick note color">
            <button type="button" id="createBtn" alt="Create new note" title="Create new note"><i class="ri-add-line"></i></button>
        </form>

        <!-- Mobile Google Ads Top -->
        <div class="mobile-ads-box">
            <div class="google-ad-wrapper google-ads1">
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

        <!-- Notes List to Populate-->
        <div id="list">
            <div id="spinner-box">
                <div id="spinner" class="spinner"></div>
            </div>
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
        let notesData = <?php echo json_encode($notes); ?>;
        document.addEventListener("DOMContentLoaded", function() { if (notesData[0]?.notes !== '') loadNoteFromDashboard(notesData[0]); });
    </script>

    <!-- <script>
        document.addEventListener("DOMContentLoaded", function () {
            const notesData = <?php echo json_encode($notes); ?>;
            if (notesData[0]?.notes !== '') loadNoteFromDashboard(notesData[0]);
        });
    </script> -->


</body>
</html>
