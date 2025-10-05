document.addEventListener("DOMContentLoaded", function () {

    // Click toàn trang
    document.addEventListener("click", function (e) {

        // Sidebar link
        const sidebarLink = e.target.closest("#sidebarnav a.sidebar-link");
        if (sidebarLink) {
            e.preventDefault();
            const url = sidebarLink.getAttribute("href");
            loadContent(url);
            window.history.pushState({}, "", url);
            setActiveSidebar(sidebarLink);
            return;
        }

        // Phân trang
        const pagelink = e.target.closest(".pagination-link");
        if (pagelink) {
            e.preventDefault();
            const url = pagelink.getAttribute("href");
            loadContent(url);
            return;
        }
    });

    window.addEventListener("popstate", function () {
        loadContent(location.href);
    });
});

function loadContent(url) {
    fetch(url)
        .then(response => response.text())
        .then(html => {
            document.getElementById("content").innerHTML = html;
        })
        .catch(err => console.error("Lỗi load content:", err));
}

function setActiveSidebar(linkEl) {
    document.querySelectorAll("#sidebarnav a.sidebar-link").forEach(a => {
        a.classList.remove("active");
    });
    linkEl.classList.add("active");
}
