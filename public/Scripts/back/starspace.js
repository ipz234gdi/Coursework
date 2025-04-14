// Constants for astronomical calculations
const EARTH_RADIUS = 6371; // km
const J2000 = new Date('2000-01-01T12:00:00Z');

class SkyMap {
    constructor(container) {
        // Scene setup
        this.scene = new THREE.Scene();
        this.camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 1000);
        this.renderer = new THREE.WebGLRenderer({ antialias: true });
        this.renderer.setSize(window.innerWidth, window.innerHeight);
        container.appendChild(this.renderer.domElement);
        
        // Sphere for the celestial dome
        this.skyDome = new THREE.SphereGeometry(100, 64, 64);
        this.skyMaterial = new THREE.MeshBasicMaterial({
            side: THREE.BackSide,
            color: 0x000000,
        });
        this.sky = new THREE.Mesh(this.skyDome, this.skyMaterial);
        this.scene.add(this.sky);
        
        // Star data structure
        this.stars = new Map();
        this.constellations = new Map();
        
        // Interactive elements
        this.raycaster = new THREE.Raycaster();
        this.mouse = new THREE.Vector2();
        
        // Initialize controls
        this.initControls();
        
        // Bind events
        this.bindEvents();
    }
    
    initControls() {
        this.controls = new THREE.OrbitControls(this.camera, this.renderer.domElement);
        this.controls.enableDamping = true;
        this.controls.dampingFactor = 0.05;
        this.controls.rotateSpeed = 0.5;
        this.camera.position.z = 5;
    }
    
    addStar(data) {
        const { ra, dec, magnitude, name } = data;
        
        // Convert celestial coordinates to Cartesian
        const phi = THREE.MathUtils.degToRad(90 - dec);
        const theta = THREE.MathUtils.degToRad(ra * 15); // RA to degrees (* 15)
        
        const starGeometry = new THREE.SphereGeometry(0.02 * (5 - magnitude), 8, 8);
        const starMaterial = new THREE.MeshBasicMaterial({ 
            color: this.getStarColor(magnitude),
        });
        
        const star = new THREE.Mesh(starGeometry, starMaterial);
        
        // Position the star on the celestial sphere
        star.position.setFromSpherical(new THREE.Spherical(100, phi, theta));
        
        this.stars.set(name, {
            mesh: star,
            data: data
        });
        
        this.scene.add(star);
    }
    
    addConstellation(name, stars) {
        const geometry = new THREE.BufferGeometry();
        const material = new THREE.LineBasicMaterial({
            color: 0x444444,
            transparent: true,
            opacity: 0.3
        });
        
        const points = [];
        stars.forEach(starPair => {
            const star1 = this.stars.get(starPair[0]);
            const star2 = this.stars.get(starPair[1]);
            
            // Check if both stars exist before adding them to constellation
            if (star1 && star2 && star1.mesh && star2.mesh) {
                points.push(star1.mesh.position, star2.mesh.position);
            } else {
                console.warn(`Missing star(s) for constellation ${name}: ${starPair[0]} or ${starPair[1]}`);
            }
        });
        
        // Only create constellation if we have points
        if (points.length >= 2) {
            geometry.setFromPoints(points);
            const lines = new THREE.LineSegments(geometry, material);
            
            this.constellations.set(name, lines);
            this.scene.add(lines);
        } else {
            console.warn(`Unable to create constellation ${name}: insufficient valid stars`);
        }
    }
    
    // Add this helper method to check if stars exist before creating constellations
    hasAllStars(starNames) {
        return starNames.every(name => this.stars.has(name));
    }
    
    getStarColor(magnitude) {
        // Simplified color based on magnitude (brightness)
        const brightness = Math.max(0.3, 1 - magnitude / 6);
        return new THREE.Color(brightness, brightness, brightness);
    }
    
    updateVisibleSky(latitude, longitude, time) {
        // Calculate local sidereal time
        const LST = this.calculateLST(longitude, time);
        
        // Update camera rotation based on observer's position
        this.camera.position.setFromSphericalCoords(
            5,
            THREE.MathUtils.degToRad(90 - latitude),
            THREE.MathUtils.degToRad(LST)
        );
        
        this.camera.lookAt(0, 0, 0);
    }
    
    calculateLST(longitude, time) {
        // Calculate Local Sidereal Time
        const JD = this.getJulianDate(time);
        const T = (JD - 2451545.0) / 36525;
        
        // Greenwich Sidereal Time
        let GST = 280.46061837 + 360.98564736629 * (JD - 2451545.0) +
                 0.000387933 * T * T - T * T * T / 38710000;
        
        // Add longitude to get Local Sidereal Time
        let LST = GST + longitude;
        
        // Normalize to 0-360 degrees
        return ((LST % 360) + 360) % 360;
    }
    
    getJulianDate(date) {
        const time = date.getTime();
        const tzoffset = date.getTimezoneOffset();
        
        return (time / 86400000) - (tzoffset / 1440) + 2440587.5;
    }
    
    showStarLabel(name, x, y) {
        const label = document.createElement('div');
        label.className = 'star-label';
        label.textContent = name;
        label.style.position = 'absolute';
        label.style.left = `${x}px`;
        label.style.top = `${y}px`;
        label.style.color = 'white';
        label.style.backgroundColor = 'rgba(0,0,0,0.7)';
        label.style.padding = '4px 8px';
        label.style.borderRadius = '4px';
        label.style.pointerEvents = 'none';
        
        document.body.appendChild(label);
        return label;
    }
    
    bindEvents() {
        let currentLabel = null;
        
        this.renderer.domElement.addEventListener('mousemove', (event) => {
            this.mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
            this.mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;
            
            this.raycaster.setFromCamera(this.mouse, this.camera);
            
            const intersects = this.raycaster.intersectObjects(
                Array.from(this.stars.values()).map(s => s.mesh)
            );
            
            if (currentLabel) {
                document.body.removeChild(currentLabel);
                currentLabel = null;
            }
            
            if (intersects.length > 0) {
                const starName = Array.from(this.stars.entries())
                    .find(([, s]) => s.mesh === intersects[0].object)[0];
                
                currentLabel = this.showStarLabel(
                    starName,
                    event.clientX + 10,
                    event.clientY + 10
                );
            }
        });
        
        window.addEventListener('resize', () => {
            this.camera.aspect = window.innerWidth / window.innerHeight;
            this.camera.updateProjectionMatrix();
            this.renderer.setSize(window.innerWidth, window.innerHeight);
        });
    }
    
    animate() {
        requestAnimationFrame(() => this.animate());
        this.controls.update();
        this.renderer.render(this.scene, this.camera);
    }
}