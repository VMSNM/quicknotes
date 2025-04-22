//GOOGLE ADS
function showFloatingAd() {
    if (window.innerWidth > 728) {
        const adBox = document.getElementById('floatingAd');
        if (adBox) {
            adBox.style.display = "flex";
        }
    }
}

function hideFloatingAd() {
    const adBox = document.getElementById('floatingAd');
    if (adBox) {
        adBox.style.display = "none";
    }
    sessionStorage.setItem("adDismissedAt", Date.now());
}

// Only show floating ad if user hasn’t recently dismissed AND screen is wide enough
const lastDismissed = sessionStorage.getItem("adDismissedAt");
const now = Date.now();
const oneMinute = 1 * 60 * 1000;

if (!lastDismissed || now - lastDismissed > oneMinute) {
    showFloatingAd();
}

// Optional: recheck periodically
setInterval(() => {
    const last = sessionStorage.getItem("adDismissedAt");
    const now = Date.now();
    if (!last || now - last > oneMinute) {
        showFloatingAd();
    }
}, 1 * 10 * 1000); // check every 10seconds

// Optional: resize event listener to hide the floating ad if the user resizes into mobile dimensions after it's been shown
window.addEventListener("resize", () => {
    if (window.innerWidth <= 728) {
        hideFloatingAd();
    }
    else { showFloatingAd(); }
});
