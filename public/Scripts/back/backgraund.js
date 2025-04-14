document.addEventListener("DOMContentLoaded", function () {
  (function () {
    const canvas = document.getElementById("canvas");
    const ctx = canvas.getContext("2d");
    let width = (canvas.width = window.innerWidth);
    let height = (canvas.height = window.innerHeight);

    window.addEventListener("resize", function () {
      const prevWidth = width;
      const prevHeight = height;

      // ⚠️ Спочатку фільтруємо, ПО СТАРИХ РОЗМІРАХ
      particles = particles.filter(
        (p) => p.x >= 0 && p.x <= prevWidth && p.y >= 0 && p.y <= prevHeight
      );

      // 🎯 Потім оновлюємо розміри
      width = canvas.width = window.innerWidth;
      height = canvas.height = window.innerHeight;

      // 🧪 Додаємо нові
      const missing = config.particleCount - particles.length;
      for (let i = 0; i < missing; i++) {
        const x = rng() * width;
        const y = rng() * height;
        const angle = rng() * Math.PI * 2;
        const speed = config.particleSpeed * (0.5 + rng());
        const vx = Math.cos(angle) * speed;
        const vy = Math.sin(angle) * speed;
        particles.push(new Particle(x, y, vx, vy));
      }
    });

    // Seeded RNG (LCG) – для повторюваності візерунку
    function createSeededRandom(seed) {
      const m = 0x80000000; // 2^31
      const a = 1103515245;
      const c = 12345;
      let state = parseInt(seed) || 12345;
      return function () {
        state = (a * state + c) % m;
        return state / m;
      };
    }

    // Конфігураційний блок
    const config = {
      particleCount: 350, // Кількість частинок
      connectionDistance: 80, // Якщо відстань між частинками менша – малюємо лінію
      particleSpeed: 1.55, // Швидкість руху (доволі повільно)
      particleRadius: 2, // Радіус частинки
      backgroundColor: "#0d0d0d", // Колір фону
      particleColor: "rgba(200,200,220,1)", // Колір частинок
      lineColor: "rgba(200,200,220,0.15)", // Колір ліній (з невеликою прозорістю)
    };

    // Клас частинки (всі частинки живі й завжди на екрані)
    class Particle {
      constructor(x, y, vx, vy) {
        this.x = x;
        this.y = y;
        this.vx = vx;
        this.vy = vy;
      }

      get xRatio() {
        return this.x / width;
      }

      get yRatio() {
        return this.y / height;
      }

      setFromRatios(xRatio, yRatio) {
        this.x = xRatio * width;
        this.y = yRatio * height;
      }

      update(dt) {
        this.x += this.vx * dt;
        this.y += this.vy * dt;
        if (this.x < 0) this.x += width;
        if (this.x > width) this.x -= width;
        if (this.y < 0) this.y += height;
        if (this.y > height) this.y -= height;
      }

      draw(ctx) {
        ctx.beginPath();
        ctx.arc(this.x, this.y, config.particleRadius, 0, Math.PI * 2);
        ctx.fillStyle = config.particleColor;
        ctx.fill();
      }
    }

    let particles = [];
    let rng;

    // Ініціалізація частинок із заданим seed
    function initParticles(seed) {
      particles = [];
      rng = createSeededRandom(seed);
      for (let i = 0; i < config.particleCount; i++) {
        const x = rng() * width;
        const y = rng() * height;
        // Невеликий випадковий кут руху з варіацією швидкості
        const angle = rng() * Math.PI * 2;
        const speed = config.particleSpeed * (0.5 + rng());
        const vx = Math.cos(angle) * speed;
        const vy = Math.sin(angle) * speed;
        particles.push(new Particle(x, y, vx, vy));
      }
    }

    // Функція для малювання візерунку
    function draw() {
      ctx.fillStyle = config.backgroundColor;
      ctx.fillRect(0, 0, width, height);
      // Малюємо зв’язки між частинками (якщо вони досить близько)
      for (let i = 0; i < particles.length; i++) {
        for (let j = i + 1; j < particles.length; j++) {
          let dx = particles[i].x - particles[j].x;
          let dy = particles[i].y - particles[j].y;
          let dist = Math.hypot(dx, dy);
          if (dist < config.connectionDistance) {
            ctx.beginPath();
            ctx.moveTo(particles[i].x, particles[i].y);
            ctx.lineTo(particles[j].x, particles[j].y);
            ctx.strokeStyle = config.lineColor;
            ctx.lineWidth = 1;
            ctx.stroke();
          }
        }
      }
      // Малюємо частинки
      for (let p of particles) {
        p.draw(ctx);
      }
    }

    let lastTime = Date.now();
    function animate() {
      const now = Date.now();
      const dt = (now - lastTime) / 1000;
      lastTime = now;

      // 🔄 Очищення частинок за межами
      particles = particles.filter(
        (p) => p.x >= 0 && p.x <= width && p.y >= 0 && p.y <= height
      );

      // ➕ Додати нові
      while (particles.length < config.particleCount) {
        const x = rng() * width;
        const y = rng() * height;
        const angle = rng() * Math.PI * 2;
        const speed = config.particleSpeed * (0.5 + rng());
        const vx = Math.cos(angle) * speed;
        const vy = Math.sin(angle) * speed;
        particles.push(new Particle(x, y, vx, vy));
      }

      // Оновлюємо позиції частинок
      for (let p of particles) {
        p.update(dt);
      }

      draw();

      requestAnimationFrame(animate);
    }

    // Інтерфейс для seed
    const seedInput = 12345;
    const resetBtn = document.getElementById("resetBtn");
    // resetBtn.addEventListener("click", function () {
    //   initParticles(seedInput.value);
    // });

    initParticles(seedInput);
    animate();
  })();
});
