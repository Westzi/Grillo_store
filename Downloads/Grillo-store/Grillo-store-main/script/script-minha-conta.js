// Alternar seções sem recarregar
document.querySelectorAll(".btn-section").forEach(btn => {
    btn.addEventListener("click", () => {
        document.querySelectorAll(".btn-section").forEach(b => b.classList.remove("active"));
        document.querySelectorAll(".section").forEach(s => s.classList.remove("active"));

        btn.classList.add("active");
        const section = btn.dataset.section;
        document.getElementById(section).classList.add("active");
    });
});

// Dark mode
const toggle = document.getElementById("darkModeToggle");
const body = document.body;
const darkIcon = toggle.querySelector("i");

if (localStorage.getItem("theme") === "dark") {
    body.classList.add("dark");
    darkIcon.classList.replace("fa-moon", "fa-sun");
}

toggle.addEventListener("click", () => {
    body.classList.toggle("dark");
    if (body.classList.contains("dark")) {
        localStorage.setItem("theme", "dark");
        darkIcon.classList.replace("fa-moon", "fa-sun");
    } else {
        localStorage.setItem("theme", "light");
        darkIcon.classList.replace("fa-sun", "fa-moon");
    }
});
