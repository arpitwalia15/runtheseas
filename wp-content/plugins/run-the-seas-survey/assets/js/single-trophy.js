(function () {
    'use strict';

    function normaliseAngle(angle) {
        var value = Number(angle) || 0;
        return ((value % 360) + 360) % 360;
    }

    function angularDistance(left, right) {
        var distance = Math.abs(normaliseAngle(left) - normaliseAngle(right));
        return Math.min(distance, 360 - distance);
    }

    function initTrophyViewer(root) {
        var model = root.querySelector('[data-rts-main-model]');
        var stage = root.querySelector('.rts-single-trophy__model-stage');
        var viewer = root.querySelector('.rts-single-trophy__viewer');
        var viewButtons = Array.prototype.slice.call(root.querySelectorAll('[data-rts-model-angle]'));
        var plaqueAnchors = Array.prototype.slice.call(root.querySelectorAll('[data-rts-main-plaque-angle]'));
        var rotateButtons = Array.prototype.slice.call(root.querySelectorAll('[data-rts-rotate]'));
        var step = Math.max(15, Math.min(90, Number(viewer && viewer.dataset.rotationStep) || 45));
        var currentAngle = 0;

        function selectNearestView(angle) {
            var nearest = null;
            var nearestDistance = Infinity;
            viewButtons.forEach(function (button) {
                var distance = angularDistance(angle, Number(button.dataset.rtsModelAngle));
                if (distance < nearestDistance) {
                    nearest = button;
                    nearestDistance = distance;
                }
            });
            viewButtons.forEach(function (button) {
                var selected = button === nearest;
                button.classList.toggle('is-current', selected);
                button.setAttribute('aria-pressed', selected ? 'true' : 'false');
            });
            var selectedAngle = nearest ? Number(nearest.dataset.rtsModelAngle) : 0;
            plaqueAnchors.forEach(function (anchor) {
                anchor.classList.toggle(
                    'is-current',
                    angularDistance(Number(anchor.dataset.rtsMainPlaqueAngle), selectedAngle) < 1
                );
            });
        }

        function moveTo(angle) {
            if (!model) {
                return;
            }
            currentAngle = Number(angle) || 0;
            model.cameraOrbit = currentAngle + 'deg 75deg auto';
            selectNearestView(currentAngle);
        }

        if (model) {
            model.addEventListener('load', function () {
                if (stage) {
                    stage.classList.add('is-model-loaded');
                }
            });
            model.addEventListener('error', function () {
                if (stage) {
                    stage.classList.remove('is-model-loaded');
                    stage.classList.add('has-model-error');
                }
            });
            model.addEventListener('camera-change', function () {
                if (typeof model.getCameraOrbit !== 'function') {
                    return;
                }
                var orbit = model.getCameraOrbit();
                if (orbit && Number.isFinite(orbit.theta)) {
                    currentAngle = orbit.theta * 180 / Math.PI;
                    selectNearestView(currentAngle);
                }
            });

            if (window.customElements && typeof window.customElements.whenDefined === 'function') {
                window.customElements.whenDefined('model-viewer').then(function () {
                    if (model.loaded && stage) {
                        stage.classList.add('is-model-loaded');
                    }
                });
            }
        }

        viewButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                moveTo(Number(button.dataset.rtsModelAngle));
            });
        });

        rotateButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                moveTo(currentAngle + (button.dataset.rtsRotate === 'previous' ? -step : step));
            });
        });

        root.addEventListener('keydown', function (event) {
            if (event.defaultPrevented || /^(INPUT|TEXTAREA|SELECT)$/.test(event.target.tagName)) {
                return;
            }
            if (event.key === 'ArrowLeft') {
                event.preventDefault();
                moveTo(currentAngle - step);
            } else if (event.key === 'ArrowRight') {
                event.preventDefault();
                moveTo(currentAngle + step);
            }
        });

        var shareButton = root.querySelector('[data-rts-share]');
        var shareStatus = root.querySelector('[data-rts-share-status]');
        var statusTimer = 0;

        function showShareStatus(message) {
            if (!shareStatus) {
                return;
            }
            window.clearTimeout(statusTimer);
            shareStatus.textContent = message;
            statusTimer = window.setTimeout(function () {
                shareStatus.textContent = '';
            }, 2600);
        }

        if (shareButton) {
            shareButton.addEventListener('click', function () {
                var shareData = {
                    title: shareButton.dataset.shareTitle || document.title,
                    text: shareButton.dataset.shareText || '',
                    url: window.location.href
                };
                if (navigator.share) {
                    navigator.share(shareData).catch(function (error) {
                        if (error && error.name !== 'AbortError') {
                            showShareStatus('Unable to open sharing.');
                        }
                    });
                    return;
                }
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(shareData.url).then(function () {
                        showShareStatus('Trophy link copied.');
                    }).catch(function () {
                        showShareStatus('Copy this page URL to share your trophy.');
                    });
                    return;
                }
                showShareStatus('Copy this page URL to share your trophy.');
            });
        }
    }

    function init() {
        document.querySelectorAll('[data-rts-single-trophy]').forEach(initTrophyViewer);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
