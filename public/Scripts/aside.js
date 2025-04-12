document.addEventListener("DOMContentLoaded", function () {
  const root = document.documentElement;
  const sidebar = document.querySelector(".sidebar");
  const toggleBtn = document.getElementById("toggleSidebarBtn");
  const toggleBtnMin = document.getElementById("toggleSidebarBtnMin");
  let hidden = localStorage.getItem("sidebarHidden") === "true";

  toggleBtn.addEventListener("click", function () {
    hidden = !hidden;
    setSidebarVisible(!hidden);
  });

  toggleBtnMin.addEventListener("click", function () {
    hidden = !hidden;
    setSidebarVisible(!hidden);
  });

  function setSidebarVisible(visible) {
    if (window.innerWidth > 1200) {
      const target = visible
        ? "var(--sidebar-width)"
        : "var(--sidebar-hidden-width)";
      root.style.setProperty("--sidebar-visible", target);

      if (sidebar) sidebar.classList.toggle("hidden", !visible);
      hidden = !visible;
      localStorage.setItem("sidebarHidden", hidden ? "true" : "false");
      updateButtonPosition();
    } else {
      const target = visible
        ? "var(--sidebar-width)"
        : "var(--sidebar-hidden-width-min)";
      root.style.setProperty("--sidebar-visible", target);

      if (sidebar) sidebar.classList.toggle("hidden", !visible);
      hidden = !visible;
      localStorage.setItem("sidebarHidden", hidden ? "true" : "false");
      updateButtonPosition();
    } 
  }

  function updateButtonPosition() {
    toggleBtn.style.left = hidden ? "14px" : "254px";
  }

  function checkResponsive() {
    if (window.innerWidth < 1200) {
        // hidden = false;
        setSidebarVisible(false);
    } else {
    //   setSidebarVisible(hidden);
    }
  }

  checkResponsive();
//   window.addEventListener("resize", checkResponsive);
});
