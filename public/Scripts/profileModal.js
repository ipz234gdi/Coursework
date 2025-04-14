function showModalProfile() {
  const modal = document.querySelector(".header-module");
  modal.classList.toggle("visible");

  // Додаємо слухача один раз
  if (modal.classList.contains("visible")) {
    document.addEventListener("click", handleOutsideClick);
  } else {
    document.removeEventListener("click", handleOutsideClick);
  }
}

function handleOutsideClick(e) {
  const modal = document.querySelector(".header-module");
  const toggleBtn = document.getElementById("userProfileBtn"); // якщо є кнопка

  // Якщо клік НЕ по модалці і НЕ по кнопці
  if (
    !modal.contains(e.target) &&
    (!toggleBtn || !toggleBtn.contains(e.target))
  ) {
    modal.classList.remove("visible");
    document.removeEventListener("click", handleOutsideClick);
  }
}
