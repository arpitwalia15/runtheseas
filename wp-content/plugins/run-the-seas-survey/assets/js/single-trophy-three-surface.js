import * as THREE from 'https://esm.sh/three@0.185.0';
import { GLTFLoader } from 'https://esm.sh/three@0.185.0/examples/jsm/loaders/GLTFLoader.js';

const modelPromises = new Map();

function normaliseAngle(angle) {
    const value = Number(angle) || 0;
    return ((value % 360) + 360) % 360;
}

function angularDistance(left, right) {
    const distance = Math.abs(normaliseAngle(left) - normaliseAngle(right));
    return Math.min(distance, 360 - distance);
}

function loadModel(url) {
    if (!modelPromises.has(url)) {
        modelPromises.set(url, new GLTFLoader().loadAsync(url));
    }
    return modelPromises.get(url);
}

function fitText(context, text, maximumWidth, startingSize, weight) {
    let size = startingSize;
    do {
        context.font = `${weight || 700} ${size}px Arial, sans-serif`;
        if (context.measureText(text).width <= maximumWidth) break;
        size -= 2;
    } while (size > 20);
}

function createPlaqueTexture(root, renderer) {
    const canvas = document.createElement('canvas');
    canvas.width = 1200;
    canvas.height = 640;
    const context = canvas.getContext('2d');
    const lines = [
        { text: root.dataset.plaqueHeading || 'MARATHON', y: 66, size: 76, weight: 800, color: '#ffd878' },
        { text: root.dataset.plaqueMilestone || 'TROPHY', y: 142, size: 72, weight: 800, color: '#ffd878' },
        { text: root.dataset.plaqueMember || '', y: 238, size: 62, weight: 800, color: '#fff0bd' },
        { text: root.dataset.plaqueRunner || '', y: 316, size: 42, weight: 700, color: '#f7d995' },
        { text: root.dataset.plaqueReferrals || '', y: 390, size: 40, weight: 700, color: '#efc36e' }
    ];

    context.clearRect(0, 0, canvas.width, canvas.height);
    context.textAlign = 'center';
    context.textBaseline = 'middle';
    context.shadowColor = 'rgba(0, 0, 0, .9)';
    context.shadowBlur = 7;
    context.strokeStyle = 'rgba(0, 0, 0, .92)';
    context.lineJoin = 'round';
    lines.forEach((line) => {
        const text = String(line.text).toUpperCase();
        fitText(context, text, 1090, line.size, line.weight);
        context.lineWidth = Math.max(3, line.size * 0.055);
        context.strokeText(text, canvas.width / 2, line.y);
        context.fillStyle = line.color;
        context.fillText(text, canvas.width / 2, line.y);
    });

    const dayColumns = [
        {
            x: 355,
            label: root.dataset.plaqueSplitLabel || 'SPLIT DAYS',
            value: root.dataset.plaqueSplitDays || '0'
        },
        {
            x: 845,
            label: root.dataset.plaqueTotalLabel || 'TOTAL DAYS',
            value: root.dataset.plaqueTotalDays || '0'
        }
    ];
    context.textAlign = 'center';
    context.shadowBlur = 6;
    dayColumns.forEach((column) => {
        context.font = '700 34px Arial, sans-serif';
        context.lineWidth = 3;
        context.strokeText(String(column.label).toUpperCase(), column.x, 474);
        context.fillStyle = '#efc36e';
        context.fillText(String(column.label).toUpperCase(), column.x, 474);
        context.font = '700 68px Georgia, serif';
        context.lineWidth = 4;
        context.strokeText(String(column.value), column.x, 557);
        context.fillStyle = '#ffe7a4';
        context.fillText(String(column.value), column.x, 557);
    });
    context.save();
    context.shadowBlur = 0;
    context.strokeStyle = 'rgba(239, 195, 110, .9)';
    context.lineWidth = 3;
    context.beginPath();
    context.moveTo(canvas.width / 2, 445);
    context.lineTo(canvas.width / 2, 585);
    context.stroke();
    context.restore();

    const texture = new THREE.CanvasTexture(canvas);
    texture.colorSpace = THREE.SRGBColorSpace;
    texture.anisotropy = Math.min(8, renderer.capabilities.getMaxAnisotropy());
    texture.needsUpdate = true;
    return texture;
}

function findPlinthObject(model) {
    const names = ['plinth', 'trophy_plinth', 'base_plinth', 'pedestal'];
    let object = null;
    names.some((name) => {
        object = model.getObjectByName(name);
        return Boolean(object);
    });

    // Replacement trophies are not required to use the original node names.
    // For example, the 5K wave model is one `Trophy` node whose front panel is
    // exposed as a mesh using the `BlackPlaque` material. Prefer that real
    // surface over the small whole-model fallback used for unknown assets.
    if (!object) {
        const plaqueCandidates = [];
        model.traverse((child) => {
            if (!child.isMesh || !child.material) return;
            const materials = Array.isArray(child.material) ? child.material : [child.material];
            const materialNames = materials.map((material) => String(material && material.name || '').toLowerCase());
            const objectName = String(child.name || '').toLowerCase();
            if (materialNames.some((name) => name.includes('plaque')) || objectName.includes('plaque')) {
                plaqueCandidates.push(child);
            }
        });
        object = plaqueCandidates[0] || null;
    }

    return object;
}

function findPlinthBox(model) {
    const object = findPlinthObject(model);
    return object ? new THREE.Box3().setFromObject(object) : null;
}

function enablePlinthStencilMask(plinthObject) {
    if (!plinthObject) return false;
    let hasMesh = false;
    plinthObject.traverse((child) => {
        if (!child.isMesh || !child.material) return;
        hasMesh = true;
        const materials = Array.isArray(child.material) ? child.material : [child.material];
        const maskedMaterials = materials.map((source) => {
            const material = source.clone();
            material.stencilWrite = true;
            material.stencilRef = 1;
            material.stencilFunc = THREE.AlwaysStencilFunc;
            material.stencilFail = THREE.KeepStencilOp;
            material.stencilZFail = THREE.KeepStencilOp;
            material.stencilZPass = THREE.ReplaceStencilOp;
            return material;
        });
        child.material = Array.isArray(child.material) ? maskedMaterials : maskedMaterials[0];
        child.renderOrder = 1;
    });
    return hasMesh;
}

function addPlaqueMeshes(model, root, renderer) {
    const modelBox = new THREE.Box3().setFromObject(model);
    const modelSize = modelBox.getSize(new THREE.Vector3());
    const plinthObject = findPlinthObject(model);
    const plinthBox = plinthObject ? new THREE.Box3().setFromObject(plinthObject) : null;
    const hasPlinthMask = enablePlinthStencilMask(plinthObject);
    const box = plinthBox || modelBox;
    const size = box.getSize(new THREE.Vector3());
    const center = box.getCenter(new THREE.Vector3());
    const widthX = plinthBox ? size.x * 0.82 : modelSize.x * 0.48;
    const height = plinthBox ? size.y * 0.66 : modelSize.y * 0.17;
    const y = plinthBox ? center.y + size.y * 0.025 : modelBox.min.y + modelSize.y * 0.2;
    const offset = Math.max(modelSize.length() * 0.008, size.z * 0.035, 0.0005);
    const texture = createPlaqueTexture(root, renderer);
    const material = new THREE.MeshBasicMaterial({
        map: texture,
        transparent: true,
        // A detected plaque writes its visible silhouette into the stencil
        // buffer. Let that mask, rather than a nearly coplanar depth test,
        // control the inscription so lower rows do not disappear into a
        // sloped plaque when viewed straight on.
        depthTest: !hasPlinthMask,
        depthWrite: false,
        side: THREE.FrontSide,
        toneMapped: false,
        polygonOffset: true,
        polygonOffsetFactor: -2,
        polygonOffsetUnits: -2,
        stencilWrite: hasPlinthMask,
        stencilRef: 1,
        stencilFunc: THREE.EqualStencilFunc,
        stencilFail: THREE.KeepStencilOp,
        stencilZFail: THREE.KeepStencilOp,
        stencilZPass: THREE.KeepStencilOp
    });
    const plane = new THREE.Mesh(new THREE.PlaneGeometry(widthX, height), material);
    plane.name = 'rts_front_trophy_plaque';
    let localPosition = new THREE.Vector3(center.x, y, box.max.z + offset);
    let localQuaternion = new THREE.Quaternion();

    if (plinthObject) {
        model.updateWorldMatrix(true, true);
        const raycaster = new THREE.Raycaster(
            new THREE.Vector3(center.x, y, box.max.z + modelSize.length()),
            new THREE.Vector3(0, 0, -1),
            0,
            modelSize.length() * 2
        );
        const hit = raycaster.intersectObject(plinthObject, true).find((intersection) => intersection.face);
        if (hit) {
            const surfaceNormal = hit.face.normal.clone().transformDirection(hit.object.matrixWorld).normalize();
            const worldPosition = hit.point.clone().addScaledVector(surfaceNormal, offset);
            localPosition = model.worldToLocal(worldPosition);

            const worldQuaternion = new THREE.Quaternion().setFromUnitVectors(
                new THREE.Vector3(0, 0, 1),
                surfaceNormal
            );
            const inverseParentQuaternion = model.getWorldQuaternion(new THREE.Quaternion()).invert();
            localQuaternion = inverseParentQuaternion.multiply(worldQuaternion);
        }
    }

    plane.position.copy(localPosition);
    plane.quaternion.copy(localQuaternion);
    plane.renderOrder = 5;
    model.add(plane);
}

function addDisplayPlatform(scene, model, modelSize, options) {
    const plinthBox = findPlinthBox(model);
    const plinthSize = plinthBox ? plinthBox.getSize(new THREE.Vector3()) : modelSize;
    const footprint = Math.max(plinthSize.x, plinthSize.z, modelSize.x * 0.82, modelSize.z * 0.82);
    const radius = footprint * (options.interactive ? 0.9 : 0.72);
    const height = modelSize.y * 0.075;
    const topHeight = height * 0.14;
    const clearance = modelSize.y * 0.012;
    const modelBottom = -(modelSize.y / 2);
    const topSurface = modelBottom - clearance;
    const baseTop = topSurface - topHeight;
    const y = baseTop - (height / 2);
    const platform = new THREE.Group();
    platform.name = 'rts_trophy_display_platform';

    const base = new THREE.Mesh(
        new THREE.CylinderGeometry(radius * 0.94, radius, height, 96, 1, false),
        new THREE.MeshStandardMaterial({
            color: 0x07131d,
            metalness: 0.9,
            roughness: 0.16
        })
    );
    base.position.y = y;
    platform.add(base);

    const top = new THREE.Mesh(
        new THREE.CylinderGeometry(radius * 0.92, radius * 0.92, topHeight, 96),
        new THREE.MeshStandardMaterial({
            color: 0x14212b,
            emissive: 0x141006,
            emissiveIntensity: 0.3,
            metalness: 0.75,
            roughness: 0.14
        })
    );
    top.position.y = baseTop + (topHeight / 2);
    platform.add(top);

    const ringMaterial = new THREE.MeshStandardMaterial({
        color: 0xd99a2b,
        emissive: 0x6b3900,
        emissiveIntensity: 0.85,
        metalness: 1,
        roughness: 0.16
    });
    [
        { ringRadius: radius * 0.94, ringY: topSurface - (topHeight * 0.16) },
        { ringRadius: radius * 0.985, ringY: y - height * 0.42 }
    ].forEach(({ ringRadius, ringY }) => {
        const ring = new THREE.Mesh(
            new THREE.TorusGeometry(ringRadius, Math.max(height * 0.045, radius * 0.004), 10, 96),
            ringMaterial
        );
        ring.rotation.x = Math.PI / 2;
        ring.position.y = ringY;
        platform.add(ring);
    });

    scene.add(platform);
    return {
        height: height + topHeight + clearance,
        radius,
        bottomY: y - (height / 2),
        topY: topSurface
    };
}

function setCameraAngle(camera, target, angle, radius, polarAngle) {
    const spherical = new THREE.Spherical(radius, polarAngle, THREE.MathUtils.degToRad(angle));
    camera.position.copy(target).add(new THREE.Vector3().setFromSpherical(spherical));
    camera.lookAt(target);
}

async function createThreeViewer(element, root, options) {
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, stencil: true, powerPreference: 'high-performance' });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, options.interactive ? 2 : 1.5));
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.15;
    renderer.setClearColor(0x000000, 0);
    element.replaceChildren(renderer.domElement);

    const scene = new THREE.Scene();
    scene.add(new THREE.HemisphereLight(0xffe4ae, 0x07111c, 2.3));
    const keyLight = new THREE.DirectionalLight(0xffd18a, 4.2);
    keyLight.position.set(2.5, 3.5, 4);
    scene.add(keyLight);
    const rimLight = new THREE.DirectionalLight(0xd4e7ff, 2.2);
    rimLight.position.set(-3, 2, -4);
    scene.add(rimLight);

    const camera = new THREE.PerspectiveCamera(options.interactive ? 31 : 34, 1, 0.001, 100);
    const gltf = await loadModel(element.dataset.modelUrl);
    const model = gltf.scene.clone(true);
    addPlaqueMeshes(model, root, renderer);

    const bounds = new THREE.Box3().setFromObject(model);
    const center = bounds.getCenter(new THREE.Vector3());
    const size = bounds.getSize(new THREE.Vector3());
    model.position.sub(center);
    const rotatingTrophy = new THREE.Group();
    rotatingTrophy.name = 'rts_rotating_trophy';
    rotatingTrophy.add(model);
    scene.add(rotatingTrophy);
    const platform = addDisplayPlatform(scene, model, size, options);
    const target = new THREE.Vector3(0, (size.y / 2 + platform.bottomY) / 2, 0);
    const framedHeight = size.y + platform.height;
    const framedWidth = platform.radius * 2.08;
    const viewer = options.interactive ? element.closest('.rts-single-trophy__viewer') : null;
    const frameHeight = Math.max(1, viewer ? viewer.clientHeight : element.clientHeight);
    const canvasHeight = Math.max(frameHeight, element.clientHeight);
    const viewerRect = viewer ? viewer.getBoundingClientRect() : null;
    const canvasRect = element.getBoundingClientRect();
    const topOverlap = viewerRect ? Math.max(0, viewerRect.top - canvasRect.top) : 0;
    const bottomOverlap = viewerRect ? Math.max(0, canvasRect.bottom - viewerRect.bottom) : 0;
    const viewportAspect = Math.max(0.2, element.clientWidth / frameHeight);
    const verticalFov = THREE.MathUtils.degToRad(camera.fov);
    const horizontalFov = 2 * Math.atan(Math.tan(verticalFov / 2) * viewportAspect);
    const verticalDistance = framedHeight / (2 * Math.tan(verticalFov / 2));
    const horizontalDistance = framedWidth / (2 * Math.tan(horizontalFov / 2));
    const baseDistance = Math.max(verticalDistance, horizontalDistance) * (options.interactive ? 1.08 : 1.15);
    const initialDistance = baseDistance * (canvasHeight / frameHeight);
    // The main canvas extends upward over the heading. Compensate for its
    // larger drawing surface so the trophy and pedestal keep exactly the same
    // resting position until the member deliberately enlarges the trophy.
    target.y += (topOverlap - bottomOverlap) * baseDistance * Math.tan(verticalFov / 2) / frameHeight;
    const polarAngle = Math.PI / 2;
    const cameraDistance = initialDistance;
    let trophyScale = 1;
    let currentAngle = Number(options.angle) || 0;
    setCameraAngle(camera, target, 0, cameraDistance, polarAngle);
    rotatingTrophy.rotation.y = THREE.MathUtils.degToRad(currentAngle);
    element.dataset.currentModelAngle = String(normaliseAngle(currentAngle));

    const controls = new THREE.EventDispatcher();
    let angleAnimationFrame = 0;
    let angleAnimationFallback = 0;
    const render = () => renderer.render(scene, camera);
    const applyTrophyScale = (scale) => {
        trophyScale = scale;
        rotatingTrophy.scale.setScalar(trophyScale);
        // Scaling a centred model normally pushes its lower half through the
        // pedestal. Raise it by the same growth amount so the bottom remains
        // seated at its original height and the trophy grows upward.
        rotatingTrophy.position.y = (trophyScale - 1) * (size.y / 2);
        render();
    };
    const applyTrophyAngle = (angle) => {
        currentAngle = angle;
        rotatingTrophy.rotation.y = THREE.MathUtils.degToRad(currentAngle);
        element.dataset.currentModelAngle = String(normaliseAngle(currentAngle));
        render();
        controls.dispatchEvent({ type: 'change' });
    };
    const resize = () => {
        const width = Math.max(1, element.clientWidth);
        const height = Math.max(1, element.clientHeight);
        renderer.setSize(width, height, false);
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        render();
    };
    new ResizeObserver(resize).observe(element);
    resize();

    if (options.interactive) {
        let dragging = false;
        let pointerX = 0;
        renderer.domElement.addEventListener('pointerdown', (event) => {
            window.cancelAnimationFrame(angleAnimationFrame);
            dragging = true;
            pointerX = event.clientX;
            renderer.domElement.setPointerCapture(event.pointerId);
        });
        renderer.domElement.addEventListener('pointermove', (event) => {
            if (!dragging) return;
            const deltaX = event.clientX - pointerX;
            pointerX = event.clientX;
            applyTrophyAngle(currentAngle + deltaX * 0.42);
        });
        const finishDrag = (event) => {
            dragging = false;
            if (renderer.domElement.hasPointerCapture(event.pointerId)) {
                renderer.domElement.releasePointerCapture(event.pointerId);
            }
        };
        renderer.domElement.addEventListener('pointerup', finishDrag);
        renderer.domElement.addEventListener('pointercancel', finishDrag);
        renderer.domElement.addEventListener('wheel', (event) => {
            event.preventDefault();
            applyTrophyScale(
                THREE.MathUtils.clamp(
                    trophyScale * Math.exp(event.deltaY * -0.001),
                    0.72,
                    1.38
                )
            );
        }, { passive: false });
        renderer.setAnimationLoop(render);
    }

    element.classList.add('is-three-ready');
    return {
        getAngle() {
            return currentAngle;
        },
        setAngle(angle, animate = true) {
            window.cancelAnimationFrame(angleAnimationFrame);
            window.clearTimeout(angleAnimationFallback);

            if (!options.interactive || !animate) {
                applyTrophyAngle(angle);
                return;
            }

            const startAngle = THREE.MathUtils.degToRad(currentAngle);
            const destinationAngle = THREE.MathUtils.degToRad(angle);
            const angleDelta = Math.atan2(
                Math.sin(destinationAngle - startAngle),
                Math.cos(destinationAngle - startAngle)
            );
            const startedAt = performance.now();
            const duration = 420;

            const animateAngle = (now) => {
                const progress = Math.min(1, (now - startedAt) / duration);
                const eased = 1 - Math.pow(1 - progress, 3);
                applyTrophyAngle(THREE.MathUtils.radToDeg(startAngle + angleDelta * eased));
                if (progress < 1) {
                    angleAnimationFrame = window.requestAnimationFrame(animateAngle);
                } else {
                    window.clearTimeout(angleAnimationFallback);
                    applyTrophyAngle(angle);
                }
            };
            angleAnimationFrame = window.requestAnimationFrame(animateAngle);
            angleAnimationFallback = window.setTimeout(() => applyTrophyAngle(angle), duration + 100);
        },
        controls
    };
}

function initShare(root) {
    const shareButton = root.querySelector('[data-rts-share]');
    const shareStatus = root.querySelector('[data-rts-share-status]');
    let statusTimer = 0;
    const showStatus = (message) => {
        if (!shareStatus) return;
        window.clearTimeout(statusTimer);
        shareStatus.textContent = message;
        statusTimer = window.setTimeout(() => { shareStatus.textContent = ''; }, 2600);
    };
    if (!shareButton) return;
    shareButton.addEventListener('click', () => {
        const data = {
            title: shareButton.dataset.shareTitle || document.title,
            text: shareButton.dataset.shareText || '',
            url: window.location.href
        };
        if (navigator.share) {
            navigator.share(data).catch((error) => {
                if (error && error.name !== 'AbortError') showStatus('Unable to open sharing.');
            });
        } else if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(data.url).then(() => showStatus('Trophy link copied.')).catch(() => showStatus('Copy this page URL to share your trophy.'));
        } else {
            showStatus('Copy this page URL to share your trophy.');
        }
    });
}

async function initTrophyViewer(root) {
    const mainElement = root.querySelector('[data-rts-main-model]');
    const stage = root.querySelector('.rts-single-trophy__model-stage');
    const viewer = root.querySelector('.rts-single-trophy__viewer');
    const viewButtons = Array.from(root.querySelectorAll('[data-rts-model-angle]'));
    const rotateButtons = Array.from(root.querySelectorAll('[data-rts-rotate]'));
    const step = Math.max(15, Math.min(90, Number(viewer && viewer.dataset.rotationStep) || 45));
    let currentAngle = 0;
    let controller = null;

    const selectNearestView = (angle) => {
        let nearest = null;
        let nearestDistance = Infinity;
        viewButtons.forEach((button) => {
            const distance = angularDistance(angle, Number(button.dataset.rtsModelAngle));
            if (distance < nearestDistance) {
                nearest = button;
                nearestDistance = distance;
            }
        });
        viewButtons.forEach((button) => {
            const selected = button === nearest;
            button.classList.toggle('is-current', selected);
            button.setAttribute('aria-pressed', selected ? 'true' : 'false');
        });
    };
    const moveTo = (angle) => {
        currentAngle = Number(angle) || 0;
        selectNearestView(currentAngle);
        if (controller) controller.setAngle(currentAngle);
    };

    if (mainElement) {
        try {
            controller = await createThreeViewer(mainElement, root, { interactive: true, angle: currentAngle });
            stage.classList.add('is-model-loaded');
            controller.controls.addEventListener('change', () => {
                currentAngle = controller.getAngle();
                selectNearestView(currentAngle);
            });
        } catch (error) {
            stage.classList.add('has-model-error');
            console.error('Unable to load the Three.js trophy model.', error);
        }
    }
    root.querySelectorAll('[data-rts-thumbnail-model]').forEach((element) => {
        createThreeViewer(element, root, { interactive: false, angle: Number(element.dataset.modelAngle) || 0 }).catch((error) => {
            element.classList.add('has-model-error');
            console.error('Unable to load a Three.js trophy preview.', error);
        });
    });
    viewButtons.forEach((button) => button.addEventListener('click', () => moveTo(Number(button.dataset.rtsModelAngle))));
    rotateButtons.forEach((button) => button.addEventListener('click', () => moveTo(currentAngle + (button.dataset.rtsRotate === 'previous' ? -step : step))));
    root.addEventListener('keydown', (event) => {
        if (event.defaultPrevented || /^(INPUT|TEXTAREA|SELECT)$/.test(event.target.tagName)) return;
        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            moveTo(currentAngle - step);
        } else if (event.key === 'ArrowRight') {
            event.preventDefault();
            moveTo(currentAngle + step);
        }
    });
    initShare(root);
}

document.querySelectorAll('[data-rts-single-trophy]').forEach((root) => {
    initTrophyViewer(root).catch((error) => console.error('Unable to initialize trophy viewer.', error));
});
