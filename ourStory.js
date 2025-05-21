document.addEventListener("DOMContentLoaded", function () {
    // Add any JavaScript functionality here
    console.log("Our Story page loaded");

    // Example: Add event listeners to social media icons
    const socialIcons = document.querySelectorAll(".social-icons a");
    socialIcons.forEach(icon => {
        icon.addEventListener("click", function (event) {
            event.preventDefault();
            window.open(this.href, "_blank");
        });
    });
});