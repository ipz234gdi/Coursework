document.addEventListener("DOMContentLoaded", () => {
    const corneredElements = document.querySelectorAll(".cornered");
  
    corneredElements.forEach(el => {
      el.classList.add("corner-box");
  
      ["tl", "tr", "bl", "br"].forEach(pos => {
        const dot = document.createElement("div");
        dot.classList.add("corner", pos);
        el.appendChild(dot);
      });
    });
  });