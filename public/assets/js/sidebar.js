(function() {
    const toggleBtn = document.getElementById("toggle-sidebar");
    const sidebar = document.getElementById("sidebar");
    const sidebarLinks = sidebar.querySelectorAll("a");
    
    toggleBtn.addEventListener("click", () => {
        sidebar.classList.toggle("open");
    });
    
    sidebarLinks.forEach(link => {
        link.addEventListener("click", () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove("open");
            }
        });
    });
    
    document.addEventListener("click", (e) => {
       if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
           sidebar.classList.remove("open");
       } 
    });
})();
