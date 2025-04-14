import * as THREE from "https://cdn.jsdelivr.net/npm/three@0.150.1/build/three.module.js";

document.addEventListener("DOMContentLoaded", function () {
  // ======================================
  // Налаштування симуляції
  // ======================================
  const seed = 42;
  const baseRadius = 5; // Половина розміру куба
  const particleCount = 12000; // Кількість частинок
  const sphereRadius = 0.1; // Радіус кожної частинки

  // Фізичні параметри
  const accelerationFactor = 0.95; // Притягання до цільової точки (куба)
  const damping = 0.98; // Демпфірування
  const dt = 0.016; // Часовий крок

  // Параметри додаткових сил:
  const randomForceStrength = 10.5; // Сила випадкового імпульсу
  const springConstant = 0.05; // Коефіцієнт «пружинного» з’єднання з сусідами

  // Параметри для зміни кольору за швидкістю
  const greyColor = new THREE.Color(0x808080); // Базовий сірий
  const whiteColor = new THREE.Color(0xffffff); // Біла
  const lowThreshold = 0.1; // Якщо швидкість нижча – залишається сірим
  const highThreshold = 5; // При високій швидкості – колір стає білим

  // Змінна для видимості куба
  let cubeVisible = true;

  // ======================================
  // Функція PRNG (mulberry32)
  // ======================================
  function mulberry32(a) {
    return function () {
      let t = (a += 0x6d2b79f5);
      t = Math.imul(t ^ (t >>> 15), t | 1);
      t ^= t + Math.imul(t ^ (t >>> 7), t | 61);
      return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
    };
  }
  const random = mulberry32(seed);

  // ======================================
  // Генерація базових цільових позицій для куба (поверхня куба)
  // ======================================
  const baseTargets = [];
  const cubeHalf = baseRadius;
  for (let i = 0; i < particleCount; i++) {
    // Випадковий вибір грані: 0: +X, 1: -X, 2: +Y, 3: -Y, 4: +Z, 5: -Z
    const face = Math.floor(random() * 6);
    const u = (random() * 2 - 1) * cubeHalf;
    const v = (random() * 2 - 1) * cubeHalf;
    let x, y, z;
    switch (face) {
      case 0:
        x = cubeHalf;
        y = u;
        z = v;
        break;
      case 1:
        x = -cubeHalf;
        y = u;
        z = v;
        break;
      case 2:
        x = u;
        y = cubeHalf;
        z = v;
        break;
      case 3:
        x = u;
        y = -cubeHalf;
        z = v;
        break;
      case 4:
        x = u;
        y = v;
        z = cubeHalf;
        break;
      case 5:
        x = u;
        y = v;
        z = -cubeHalf;
        break;
    }
    baseTargets.push(new THREE.Vector3(x, y, z));
  }

  // ======================================
  // Ініціалізація поточних позицій і швидкостей частинок (починають у центрі)
  // ======================================
  const currentPositions = [];
  const velocities = [];
  for (let i = 0; i < particleCount; i++) {
    currentPositions.push(new THREE.Vector3(0, 0, 0));
    velocities.push(new THREE.Vector3(0, 0, 0));
  }

  // ======================================
  // Створення сцени, камери, рендерера
  // ======================================
  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(
    75,
    window.innerWidth / window.innerHeight,
    0.1,
    1000
  );
  camera.position.z = 15;
  const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
  renderer.setSize(window.innerWidth, window.innerHeight);
  document.body.appendChild(renderer.domElement);

  // ======================================
  // Джерело світла
  // ======================================
  const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
  directionalLight.position.set(1, 1, 1);
  scene.add(directionalLight);

  // ======================================
  // Створення куба (невидимий за замовчуванням)
  // ======================================
  const cubeGeometry = new THREE.BoxGeometry(
    cubeHalf * 2,
    cubeHalf * 2,
    cubeHalf * 2
  );
  const cubeMaterial = new THREE.MeshBasicMaterial({
    color: 0x333333,
    wireframe: true,
  });
  const cubeMesh = new THREE.Mesh(cubeGeometry, cubeMaterial);
  cubeMesh.visible = cubeVisible;
  scene.add(cubeMesh);

  // ======================================
  // Створення InstancedMesh для частинок (сфер), з кольором, що змінюється
  // ======================================
  const sphereGeometry = new THREE.SphereGeometry(sphereRadius, 16, 16);
  const sphereMaterial = new THREE.MeshStandardMaterial({
    vertexColors: true,
    color: greyColor,
  });
  const spheres = new THREE.InstancedMesh(
    sphereGeometry,
    sphereMaterial,
    particleCount
  );
  scene.add(spheres);
  const instanceColors = new Float32Array(particleCount * 3);
  for (let i = 0; i < particleCount; i++) {
    instanceColors[i * 3] = greyColor.r;
    instanceColors[i * 3 + 1] = greyColor.g;
    instanceColors[i * 3 + 2] = greyColor.b;
  }
  spheres.instanceColor = new THREE.InstancedBufferAttribute(instanceColors, 3);
  const dummy = new THREE.Object3D();

  // ======================================
  // Обертання куба – базова фігура
  // ======================================
  const cubeRotation = new THREE.Euler(0, 0, 0);
  const cubeRotationSpeed = new THREE.Vector3(0, 0.2, 0);
  const rotationMatrix = new THREE.Matrix4();

  // ======================================
  // Анімаційний цикл
  // ======================================
  function animate() {
    requestAnimationFrame(animate);
    const time = Date.now() * 0.001;

    // Оновлення обертання куба
    cubeRotation.x += cubeRotationSpeed.x * dt;
    cubeRotation.y += cubeRotationSpeed.y * dt;
    cubeRotation.z += cubeRotationSpeed.z * dt;
    rotationMatrix.makeRotationFromEuler(cubeRotation);

    // Оновлення кожної частинки
    for (let i = 0; i < particleCount; i++) {
      const curr = currentPositions[i];
      // Динамічна ціль: базова точка, обернена за поточним обертанням куба
      const dynamicTarget = baseTargets[i].clone().applyMatrix4(rotationMatrix);
      const vel = velocities[i];

      // Сила притягання до динамічної цілі
      const targetForce = new THREE.Vector3()
        .subVectors(dynamicTarget, curr)
        .multiplyScalar(accelerationFactor);

      // Додатковий випадковий імпульс
      const randomForce = new THREE.Vector3(
        (random() - 0.5) * randomForceStrength,
        (random() - 0.5) * randomForceStrength,
        (random() - 0.5) * randomForceStrength
      );

      // Пружинний зв’язок із сусідніми (спрощено: використовуємо сусідніх за індексом)
      let springForce = new THREE.Vector3(0, 0, 0);
      let neighborCount = 0;
      if (i > 0) {
        springForce.add(
          new THREE.Vector3().subVectors(currentPositions[i - 1], curr)
        );
        neighborCount++;
      }
      if (i < particleCount - 1) {
        springForce.add(
          new THREE.Vector3().subVectors(currentPositions[i + 1], curr)
        );
        neighborCount++;
      }
      if (neighborCount > 0) {
        springForce.multiplyScalar(springConstant / neighborCount);
      }

      // Загальна сила: притягання до цілі + випадкова сила + сила сусідів
      const acceleration = new THREE.Vector3()
        .add(targetForce)
        .add(randomForce)
        .add(springForce);
      vel.add(acceleration.multiplyScalar(dt));
      vel.multiplyScalar(damping);
      curr.add(vel.clone().multiplyScalar(dt));

      // Оновлюємо матрицю трансформації для частинки
      dummy.position.copy(curr);
      dummy.updateMatrix();
      spheres.setMatrixAt(i, dummy.matrix);

      // Зміна кольору: якщо частинка швидко рухається – стає білою, інакше – сірою
      const speed = vel.length();
      const newColor = new THREE.Color();
      if (speed < lowThreshold) {
        newColor.copy(greyColor);
      } else if (speed > highThreshold) {
        newColor.copy(whiteColor);
      } else {
        const tColor = (speed - lowThreshold) / (highThreshold - lowThreshold);
        newColor.lerpColors(greyColor, whiteColor, tColor);
      }
      spheres.instanceColor.setXYZ(i, newColor.r, newColor.g, newColor.b);
    }
    spheres.instanceMatrix.needsUpdate = true;
    spheres.instanceColor.needsUpdate = true;

    // Оновлення куба (якщо він видимий)
    cubeMesh.visible = cubeVisible;
    cubeMesh.rotation.copy(cubeRotation);

    renderer.render(scene, camera);
  }
  animate();

  // ======================================
  // Обробка зміни розміру вікна
  // ======================================
  window.addEventListener("resize", () => {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
  });
});
