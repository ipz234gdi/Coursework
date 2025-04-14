import * as THREE from "https://cdn.jsdelivr.net/npm/three@0.150.1/build/three.module.js";

// Запускаємо після завантаження DOM
document.addEventListener("DOMContentLoaded", () => {

  // ==============================================================================
  // Налаштування параметрів симуляції кластеру частинок
  // ==============================================================================
  const particleCount = 1000;       // Налаштовувана кількість частинок
  const clusterRadius = 1;        // Радіус області, в якій розташовані частинки (початково)
  const sphereRadius = 0.1;        // Радіус кожної частинки

  // Фізичні параметри симуляції
  const springConstant = 0.1;      // "Пружинна" сила, що зв'язує частинки
  const restLength = 0.01;          // Бажана відстань між частинками (можна підібрати)
  const randomForceStrength = 0.9; // Сила випадкового імпульсу для урізноманітнення руху
  const damping = 0.99;            // Демпфірування руху
  const dt = 0.16;              // Часовий крок (~60 кадрів/сек)

  // Параметри для зміни кольору (емісія) залежно від швидкості
  const lowThreshold = 0.01;        // Швидкість нижче якої колір залишається базовим
  const highThreshold = 1.0;       // При високій швидкості частинка набуває максимальної світлості

  // Базовий відтінок частинки (наприклад, помірно синій)
  const baseColor = new THREE.Color(0xffffff);

  // ==============================================================================
  // Створення сцени, камери та рендерера
  // ==============================================================================
  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(
    75, window.innerWidth / window.innerHeight, 0.1, 1000
  );
  camera.position.z = 30;
  const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
  renderer.setSize(window.innerWidth, window.innerHeight);
  document.body.appendChild(renderer.domElement);

  // Додаємо directional light
  const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
  directionalLight.position.set(1, 1, 1);
  scene.add(directionalLight);

  // ==============================================================================
  // Створення кластеру частинок
  // ==============================================================================
  // Ми створимо масив об'єктів-частинок; кожна частинка матиме:
  // - позицію (Vector3)
  // - швидкість (Vector3)
  // - mesh (сфера)
  const particles = [];

  // Використаємо MeshStandardMaterial із емісійним кольором для glowing ефекту
  function createParticleMesh(color) {
    const geom = new THREE.SphereGeometry(sphereRadius, 16, 16);
    // Використовуємо матеріал з emissive, щоб частинка "світлилась"
    const mat = new THREE.MeshStandardMaterial({
      color: color,
      emissive: color,
      emissiveIntensity: 0.5
    });
    return new THREE.Mesh(geom, mat);
  }

  // Генеруємо початкові позиції випадково всередині сфери clusterRadius
  for (let i = 0; i < particleCount; i++) {
    // Рандомна позиція в сфері: використаємо метод випадкового напрямку
    const dir = new THREE.Vector3(
      Math.random()*2 - 1,
      Math.random()*2 - 1,
      Math.random()*2 - 1
    ).normalize();
    const r = Math.random() * clusterRadius;
    const position = dir.multiplyScalar(r);
    const velocity = new THREE.Vector3(0, 0, 0);
    const mesh = createParticleMesh(baseColor);
    mesh.position.copy(position);
    scene.add(mesh);
    particles.push({ position, velocity, mesh });
  }

  // ==============================================================================
  // Функція, що обчислює силу-пружину між двома частинками
  // ==============================================================================
  function springForce(p1, p2) {
    const dir = new THREE.Vector3().subVectors(p2, p1);
    const d = dir.length();
    // Розрахунок сили: F = -k * (d - restLength)
    const forceMagnitude = -springConstant * (d - restLength);
    return dir.normalize().multiplyScalar(forceMagnitude);
  }

  // ==============================================================================
  // Функція додаткової випадкової сили
  // ==============================================================================
  function randomImpulse() {
    return new THREE.Vector3(
      (Math.random() - 0.5) * randomForceStrength,
      (Math.random() - 0.5) * randomForceStrength,
      (Math.random() - 0.5) * randomForceStrength
    );
  }

  // ==============================================================================
  // Функція для оновлення емісійного кольору частинки залежно від її швидкості
  // ==============================================================================
  function updateParticleColor(particle) {
    const speed = particle.velocity.length();
    // Інтерполяція між базовим кольором та білим
    const t = THREE.MathUtils.clamp((speed - lowThreshold) / (highThreshold - lowThreshold), 0, 1);
    const newColor = new THREE.Color().lerpColors(
      baseColor,
      new THREE.Color(0xffffff),
      t
    );
    // Оновлюємо колір матеріалу (color і emissive)
    particle.mesh.material.color.copy(newColor);
    particle.mesh.material.emissive.copy(newColor);
  }

  // ==============================================================================
  // Основна симуляція: розрахунок сил між частинками
  // ==============================================================================
  function simulate() {
    // Для кожної частинки обчислюємо суму сил (з нуля)
    const forces = particles.map(() => new THREE.Vector3(0, 0, 0));

    // Для кожної пари частинок розраховуємо силу-пружину, якщо вони досить близько
    for (let i = 0; i < particles.length; i++) {
      for (let j = i + 1; j < particles.length; j++) {
        const p1 = particles[i].position;
        const p2 = particles[j].position;
        const d = p1.distanceTo(p2);
        const connectionThreshold = restLength * 2.5; // якщо відстань менша за певний поріг, застосовуємо силу
        if (d < connectionThreshold) {
          const f = springForce(p1, p2);
          forces[i].add(f);
          forces[j].sub(f); // за третім законом Ньютона
        }
      }
    }
    // Додаємо випадковий імпульс і оновлюємо швидкості та позиції
    particles.forEach((particle, idx) => {
      // Сумарна сила = сили взаємодії + випадкова сила
      const totalForce = forces[idx].add(randomImpulse());
      // Оновлення швидкості
      particle.velocity.add(totalForce.multiplyScalar(dt));
      particle.velocity.multiplyScalar(damping);
      // Оновлення позиції
      particle.position.add(particle.velocity.clone().multiplyScalar(dt));
      // Оновлення позиції mesh
      particle.mesh.position.copy(particle.position);
      // Оновлення кольору залежно від швидкості
      updateParticleColor(particle);
    });
  }

  // ==============================================================================
  // Створення сцени (фон, камера, постпроцесінг відсутній у цьому прикладі)
  // ==============================================================================
  function animate() {
    requestAnimationFrame(animate);
    simulate();
    renderer.render(scene, camera);
  }
  animate();

  // ==============================================================================
  // Адаптація під розмір вікна
  // ==============================================================================
  window.addEventListener("resize", () => {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
  });
});