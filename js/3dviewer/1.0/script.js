import * as THREE from 'https://cdn.skypack.dev/three@0.128.0';
import { OBJLoader } from 'https://cdn.skypack.dev/three@0.128.0/examples/jsm/loaders/OBJLoader';
import { STLLoader } from 'https://cdn.skypack.dev/three@0.128.0/examples/jsm/loaders/STLLoader';
import { FBXLoader } from 'https://cdn.skypack.dev/three@0.128.0/examples/jsm/loaders/FBXLoader';
import { OrbitControls } from 'https://cdn.skypack.dev/three@0.128.0/examples/jsm/controls/OrbitControls';
import { GLTFLoader } from 'https://cdn.skypack.dev/three@0.128.0/examples/jsm/loaders/GLTFLoader';

const modelPath = document.getElementById("modelPath");
const fileExtension = document.getElementById("fileExtension");

loadModel(modelPath.value, fileExtension.value.toLowerCase());

async function loadModel(path, fileExtension) {
    const newScene = new THREE.Scene();
    newScene.background = new THREE.Color(document.body.style.backgroundColor);

    if (fileExtension === 'stl') {
        fetch(path)
            .then((response) => response.arrayBuffer())
            .then((buffer) => {
                const loader = new STLLoader();
                const geometry = loader.parse(buffer);
                init(geometry, fileExtension, newScene);
            });
    } else if (fileExtension === 'obj') {
        fetch(path)
            .then((response) => response.text())
            .then((text) => {
                const loader = new OBJLoader();
                model = loader.parse(text);
                init(model, fileExtension, newScene);
            });
    } else if (fileExtension === 'fbx') {
        const loader = new FBXLoader();
        loader.load(path, (fbx) => {
            model = fbx;
            init(model, fileExtension, newScene);
        }, undefined, (error) => {
            console.error('При загрузке файла FBX  произошла ошибка:', error);
        });
    } else if (fileExtension === 'gltf' || fileExtension === 'glb') {
        const loader = new GLTFLoader();
        loader.load(path, (gltf) => {
            model = gltf.scene;
            init(model, fileExtension, newScene);
        }, undefined, (error) => {
            console.error('При загрузке файла glTF произошла ошибка:', error);
        });
    } else {
        console.error('Неподдерживаемый формат');
    }
}

let model;

function resizeAndCenterModel(model, camera) {
    const box = new THREE.Box3().setFromObject(model);
    const boxSize = box.getSize(new THREE.Vector3());
    const boxCenter = box.getCenter(new THREE.Vector3());
    
    const maxSize = Math.max(boxSize.x, boxSize.y, boxSize.z);
    const scale = 1.5 / maxSize;
    
    model.scale.set(scale, scale, scale);
    
    box.setFromObject(model);
    box.getCenter(boxCenter);
    
    const radius = boxSize.length() / 2;
    const fov = camera.fov * (Math.PI / 180);
    
    const cameraDistance = (radius * 1.5) / Math.sin(fov / 2);
    const offset = new THREE.Vector3();
    
    offset.copy(boxCenter);
    offset.z += cameraDistance;
    
    camera.position.copy(offset);
    camera.lookAt(boxCenter);
    camera.updateProjectionMatrix();
    
    model.position.sub(boxCenter);
}



function init(modelData, fileExtension, scene) {
    const camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 1, 1000);
    const renderer = new THREE.WebGLRenderer();
    renderer.setSize(window.innerWidth, window.innerHeight);
    document.body.appendChild(renderer.domElement);

    const loading_text = document.getElementById("loading_text");
    loading_text.style.display = 'none'

    if (fileExtension === 'stl') {
        const material = new THREE.MeshPhongMaterial({ color: 0xaaaaaa });
        model = new THREE.Mesh(modelData, material);
        model.scale.set(0.05, 0.05, 0.05);
        scene.add(model);

    } else if (fileExtension === 'obj' || fileExtension === 'fbx' || fileExtension === 'gltf' || fileExtension === 'glb') {
        model = modelData;
        model.scale.set(0.05, 0.05, 0.05);
        scene.add(model);
    }

    const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
    scene.add(ambientLight);
    const pointLight = new THREE.PointLight(0xffffff, 0.5);
    camera.add(pointLight);
    scene.add(camera);
    const controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.1;
    controls.zoomSpeed = 1.5;
    controls.panSpeed = 0.5;

    resizeAndCenterModel(model, camera);
    scene.add(model);
    camera.position.set(0, 0, 30);
    controls.update();

    const animate = function () {
        requestAnimationFrame(animate);
        controls.update();
        renderer.render(scene, camera);
    };

    animate();

    let intervalId;

    function handleButtonEvent(button, action) {
        button.addEventListener("mousedown", () => {
            intervalId = setInterval(action, 100);
        });

        button.addEventListener("mouseup", () => {
            clearInterval(intervalId);
        });

        button.addEventListener("mouseleave", () => {
            clearInterval(intervalId);
        });
    }

    handleButtonEvent(document.getElementById("center"), () => {
        camera.position.set(0, 0, 30);
    });

    const colorInput = document.getElementById("color");

   
    set_model_color(colorInput.value);

    colorInput.addEventListener("input", (event) => {
        const color = event.target.value;

        set_model_color(color);
    });

    function set_model_color(color) {
        if (fileExtension === 'stl') {
            model.material.color.set(color);
        } else if (fileExtension === 'obj' || fileExtension === 'fbx' || fileExtension === 'gltf' || fileExtension === 'glb') {
            model.traverse((child) => {
                if (child instanceof THREE.Mesh) {
                    child.material.color.set(color);
                }
            });
        }
    }
}

