<?php
    include("config.php");

    if (isset($_SESSION["user_id"])) {
        header("Location: dashboard.php", true, 301);
        exit();
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
        "url": "https://quicknotesonline.com",
        "logo": "https://quicknotesonline.com/images/quicknotes-logo.png",
        "sameAs": [
            "https://www.instagram.com/quicknotesonline",
            "https://x.com/quicknotesonlin",
            "https://www.linkedin.com/in/quick-notes-online"
        ],
        "description": "Take your notes anytime, anywhere and save them for later with the Online Notes App.",
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
  <meta property="og:url" content="https://quicknotesonline.com" />
  <meta property="og:type" content="website" />
  <!-- <meta property="og:image" content="https://quicknotesonline.com/images/preview_image.jpg" /> -->

  <!-- Canonical Link -->
  <link rel="canonical" href="https://quicknotesonline.com">

  <!-- Favicon & Fonts -->
  <link rel="icon" type="image/x-icon" href="images/quicknotesfavicon.ico">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- CSS & JS -->
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css">
  <script src="index.js" defer></script>

  <!-- Google AdSense -->
  <!-- <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3940256099942544" crossorigin="anonymous"></script> -->
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
                <a href="index.php">
                    <!-- <i class="ri-file-text-line"></i> --> 
                     <img src="images/quicknotes-logo-site.png" class="site-logo" alt="Quick Notes Logo" title="Quick Notes Logo" />
                    <h2>Quick Notes <span className="logo-dot">|</span></h2>
                </a>
            </div>
            <div class="site-header-right">
                <a href="login.php" title="Login to save your notes" class="action-btn">Login</a>
            </div>
        </div>
        

        <!-- Color Picker -->
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
        <div id="list"></div>

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
</body>
</html>