//Slide bar toggle
document.addEventListener("DOMContentLoaded", () => {
    const sidebarButtons = document.querySelectorAll(".sidebar-btn");
    const contentContainer = document.querySelector(".content");

    sidebarButtons.forEach(button => {
        button.addEventListener("click", () => {
            const page = button.getAttribute("data-page");

            sidebarButtons.forEach(btn => btn.classList.remove("active"));
            button.classList.add("active");

            fetch(page)
                .then(response => response.text())
                .then(data => {
                    contentContainer.innerHTML = data;

                    // Conditionally load script for profile_address.php
                    if (page.includes("profile_address")) {
                        const oldScript = document.querySelector("#profileAddressScript");
                        if (oldScript) oldScript.remove(); // prevent duplicate

                        const script = document.createElement("script");
                        script.id = "profileAddressScript";
                        script.src = "js/profile_address.js";
                        document.body.appendChild(script);
                    }
                });
        });
    });

    // Load profile_user_info.php by default
    document.querySelector(".sidebar-btn[data-page='includes/profile_user_info.php']").click();
});



document.querySelectorAll(".sidebar-btn").forEach(button => {
    button.addEventListener("click", function () {
        // Remove active class from all buttons
        document.querySelectorAll(".sidebar-btn").forEach(btn => btn.classList.remove("active"));
        this.classList.add("active");

        const page = this.getAttribute("data-page");

        fetch(page)
            .then(res => {
                if (!res.ok) throw new Error("Page not found");
                return res.text();
            })
            .then(data => {
                document.querySelector(".content").innerHTML = data;

                  // For Profile page: re-initialize scripts if needed
                  if (page.includes("profile_user_info")) {
                    const script = document.createElement("script");
                    script.src = "js/profile_user_info.js";
                    document.body.appendChild(script);
                }

                // For address page: re-initialize scripts if needed
                if (page.includes("profile_address")) {
                    const script = document.createElement("script");
                    script.src = "js/profile_address.js";
                    document.body.appendChild(script);
                }

                 // For Change Password page: re-initialize scripts if needed
                if (page.includes("profile_change_password")) {
                    const script = document.createElement("script");
                    script.src = "js/profile_change_password.js";
                    document.body.appendChild(script);
                }
                

            })
            .catch(err => {
                document.querySelector(".content").innerHTML = `<p class="text-danger">Error loading page: ${err.message}</p>`;
            });
    });
});
