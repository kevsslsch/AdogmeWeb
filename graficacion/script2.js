let scene,
    camera,
    renderer,
    controls,
    mouseDown,
    world,
    night = false;

let cloud, sky;

let width, height;

function init() {
    width = window.innerWidth;
    height = window.innerHeight;

    scene = new THREE.Scene();
    camera = new THREE.PerspectiveCamera(75, width / height, 0.1, 1000);
    camera.lookAt(scene.position);
    camera.position.set(0, 0.7, 8);

    renderer = new THREE.WebGLRenderer({ alpha: true });
    renderer.setPixelRatio(window.devicePixelRatio);
    renderer.setSize(width, height);
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;

    addLights();
    drawCloud();
    drawSky();

    world = document.querySelector('.world');
    world.appendChild(renderer.domElement);
    window.addEventListener('resize', onResize);
}

function addLights() {
    const light = new THREE.HemisphereLight(0xffffff, 0xffffff, 0.9);
    scene.add(light);

    const directLight1 = new THREE.DirectionalLight(0xffd798, 0.8);
    directLight1.castShadow = true;
    directLight1.position.set(9.5, 8.2, 8.3);
    scene.add(directLight1);

    const directLight2 = new THREE.DirectionalLight(0xc9ceff, 0.5);
    directLight2.castShadow = true;
    directLight2.position.set(-15.8, 5.2, 8);
    scene.add(directLight2);
}

function drawCloud() {
    cloud = new Cloud();
    scene.add(cloud.group);
}

function drawSky() {
    sky = new Sky();
    sky.showNightSky(night);
    scene.add(sky.group);
}

function onResize() {
    width = window.innerWidth;
    height = window.innerHeight;
    camera.aspect = width / height;
    camera.updateProjectionMatrix();
    renderer.setSize(width, height);
}

function rad(degrees) {
    return degrees * (Math.PI / 180);
}

function animate() {
    requestAnimationFrame(animate);
    render();
}

function render() {
    sky.moveSky();
    renderer.render(scene, camera);
}

class Cloud {
  constructor() {
    this.group = new THREE.Group();
    this.group.position.y = -10;
    this.group.scale.set(1.5, 1.5, 1.5);

    this.material = new THREE.MeshStandardMaterial({
        color: 0xacb3fb,
        roughness: 1,
        shading: THREE.FlatShading
    });

    this.vAngle = 0;
    this.drawParts();

    this.group.traverse((part) => {
        part.castShadow = true;
        part.receiveShadow = true;
    });
    }

    drawParts() {
      const partGeometry = new THREE.IcosahedronGeometry(1, 0);
      this.frontPart = new THREE.Mesh(partGeometry, this.material);
      this.group.add(this.frontPart);
      this.backPart = new THREE.Mesh(partGeometry, this.material);
      this.backPart.position.z = -this.frontPart.position.z;
      this.group.add(this.backPart);
    }

}

class Sky {
    constructor() {
        this.group = new THREE.Group();
        this.daySky = new THREE.Group();
        this.nightSky = new THREE.Group();
        this.group.add(this.daySky);
        this.group.add(this.nightSky);

        this.colors = {
            day: [0xFFFFFF, 0xEFD2DA, 0xC1EDED, 0xCCC9DE],
        };
        this.drawSky('day');
        this.drawNightLights();
    }
    drawSky(phase) {
        for (let i = 0; i < 1000; i++) {
            const geometry = new THREE.IcosahedronGeometry(0.4, 0);
            const material = new THREE.MeshStandardMaterial({
                color: this.colors[phase][Math.floor(Math.random() * this.colors[phase].length)],
                roughness: 1,
                shading: THREE.FlatShading
            });
            const mesh = new THREE.Mesh(geometry, material);

            mesh.position.set((Math.random() - 0.5) * 30,
                (Math.random() - 0.5) * 30,
                (Math.random() - 0.5) * 30);
            if (phase === 'day') {
                this.daySky.add(mesh);
            }
        }
    }
    drawNightLights() {
        const geometry = new THREE.SphereGeometry(0.1, 5, 5);
        const material = new THREE.MeshStandardMaterial({
            color: 0xFF51B6,
            roughness: 1,
            shading: THREE.FlatShading
        });
        for (let i = 0; i < 3; i++) {
            const light = new THREE.PointLight(0xF55889, 2, 30);
            const mesh = new THREE.Mesh(geometry, material);
            light.add(mesh);

            light.position.set((Math.random() - 2) * 6,
                (Math.random() - 2) * 6,
                (Math.random() - 2) * 6);
            light.updateMatrix();
            light.matrixAutoUpdate = false;

            this.nightSky.add(light);
        }
    }
    showNightSky(condition) {
        if (condition) {
            this.daySky.position.set(100, 100, 100);
            this.nightSky.position.set(0, 0, 0);
        } else {
            this.daySky.position.set(0, 0, 0);
            this.nightSky.position.set(100, 100, 100);
        }
    }
    moveSky() {
        this.group.rotation.x += 0.001;
        this.group.rotation.y -= 0.004;
    }
}
init();
animate();
