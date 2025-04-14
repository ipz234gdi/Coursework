document.addEventListener("DOMContentLoaded", function () {
  const canvas = document.getElementById("canvas");
  const ctx = canvas.getContext("2d");
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;

  // Лазерні налаштування
  let laserX = 0;
  let laserY = 0;
  let angle = 0;
  let index = 0;
  let path = [];

  // Центр екрану
  const centerX = canvas.width / 2;
  const centerY = canvas.height / 2;

  // Конфігурація симуляції
  const laserSpeed = 0.1; // швидкість переміщення точки
  const fadeAmount = 0.01; // зникаючий слід

  // Малюємо фігуру або текст
  function createShape(shape = "text") {
    path = [];
    const scale = 100;

    if (shape === "circle") {
      for (let i = 0; i <= 360; i++) {
        const rad = (i * Math.PI) / 180;
        path.push([Math.cos(rad) * scale, Math.sin(rad) * scale]);
      }
    } else if (shape === "rectangle") {
      path = [
        [-scale, -scale],
        [scale, -scale],
        [scale, scale],
        [-scale, scale],
        [-scale, -scale],
      ];
    } else if (shape === "heart") {
      for (let t = 0; t < Math.PI * 2; t += 0.05) {
        const x = 16 * Math.pow(Math.sin(t), 3);
        const y = -(
          13 * Math.cos(t) -
          5 * Math.cos(2 * t) -
          2 * Math.cos(3 * t) -
          Math.cos(4 * t)
        );
        path.push([x * scale * 0.5, y * scale * 0.5]);
      }
    } else {
      // Текст — використовуємо вбудовані шляхи
      ctx.font = "bold 80px monospace";
      ctx.textBaseline = "middle";
      ctx.textAlign = "left";
      const text = "I fack your mouther";
      const metrics = ctx.measureText(text);
      const offsetX = metrics.width / 2;

      const tempCanvas = document.createElement("canvas");
      tempCanvas.width = canvas.width;
      tempCanvas.height = canvas.height;
      const tempCtx = tempCanvas.getContext("2d");
      tempCtx.font = ctx.font;
      tempCtx.fillStyle = "white";
      tempCtx.fillText(text, centerX - 400, centerY);

      const imageData = tempCtx.getImageData(
        0,
        0,
        canvas.width,
        canvas.height
      ).data;

      for (let y = 0; y < canvas.height; y += 4) {
        for (let x = 0; x < canvas.width; x += 4) {
          const i = (y * canvas.width + x) * 4;
          if (imageData[i] > 100) {
            path.push([x - centerX, y - centerY]);
          }
        }
      }
    }
  }

  // Анімація лазера
  const pointsPerFrame = 50; // Скільки точок проходить лазер за один кадр

  function animate() {
    ctx.fillStyle = `rgba(0, 0, 0, ${fadeAmount})`;
    ctx.fillRect(0, 0, canvas.width, canvas.height);
  
    for (let i = 0; i < pointsPerFrame && path.length > 0; i++) {
      const [targetX, targetY] = path[index % path.length];
      laserX = targetX;
      laserY = targetY;
  
      ctx.beginPath();
      ctx.arc(centerX + laserX, centerY + laserY, 1.5, 0, Math.PI * 2);
      ctx.fillStyle = "lime";
      ctx.fill();
  
      index++;
    }
  
    requestAnimationFrame(animate);
  }

  // Запуск
  createShape("text"); // можна також 'circle', 'rectangle', 'heart'
  animate();
});
