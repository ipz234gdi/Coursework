document.addEventListener("DOMContentLoaded", function () {
  const root = document.documentElement;
  const sidebar = document.querySelector(".sidebar");
  const sidebar_wrapper = document.querySelector(".sidebar-wrapper");
  const toggleBtn = document.getElementById("toggleSidebarBtn");
  const toggleBtnMin = document.getElementById("toggleSidebarBtnMin");
  let hidden = localStorage.getItem("sidebarHidden") === "true";
  let hoverTimeout;
  let isMin1200 = false;

  toggleBtn?.addEventListener("click", () => {
    hidden = !hidden;
    setSidebarVisible(!hidden);
  });

  toggleBtnMin?.addEventListener("click", () => {
    hidden = !hidden;
    setSidebarVisible(!hidden);
  });

  // Hover ефект для розгортання
  sidebar?.addEventListener("mouseover", (event) => {
    if (
      hidden &&
      window.innerWidth >= 1200 &&
      !sidebar_wrapper.contains(event.relatedTarget)
    ) {
      clearTimeout(hoverTimeout);
      hoverTimeout = setTimeout(() => {
        root.style.setProperty("--sidebar-visible", "var(--sidebar-width)");
        sidebar_wrapper?.classList.remove("hidden");
      }, 300);
    }
  });

  sidebar?.addEventListener("mouseout", (event) => {
    if (
      hidden &&
      window.innerWidth >= 1200 &&
      !sidebar_wrapper.contains(event.relatedTarget)
    ) {
      clearTimeout(hoverTimeout);
      hoverTimeout = setTimeout(() => {
        root.style.setProperty("--sidebar-visible", "30px");
        sidebar_wrapper?.classList.add("hidden");
      }, 300);
    }
  });

  function setSidebarVisible(visible) {
    const isDesktop = window.innerWidth >= 1200;

    const target = visible
      ? "var(--sidebar-width)"
      : isDesktop
      ? "var(--sidebar-hidden-width)"
      : "var(--sidebar-hidden-width-min)";

    root.style.setProperty("--sidebar-visible", target);
    sidebar_wrapper?.classList.toggle("hidden", !visible);
    hidden = !visible;
    localStorage.setItem("sidebarHidden", hidden ? "true" : "false");
  }

  function checkResponsive() {
    if (window.innerWidth < 1200) {
      if (isMin1200 === false) {
        setSidebarVisible(false);
        isMin1200 = true;
      }
    } else {
      if (isMin1200 === true) {
        setSidebarVisible(true);
        isMin1200 = false;
      }
    }
    // console.log(isMin1200);
  }

  checkResponsive();
  window.addEventListener("resize", checkResponsive);
});
