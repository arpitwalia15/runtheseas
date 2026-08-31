(function () {
    'use strict';

    function initChallenge(root) {
        var groups = Array.prototype.slice.call(root.querySelectorAll('[data-rts-mc-popover]'));

        function closeAll(except) {
            groups.forEach(function (group) {
                if (group === except) {
                    return;
                }
                group.classList.remove('is-open');
                var button = group.querySelector(':scope > button');
                if (button) {
                    button.setAttribute('aria-expanded', 'false');
                }
            });
        }

        groups.forEach(function (group) {
            var button = group.querySelector(':scope > button');
            var popup = group.querySelector(':scope > .rts-mc-popover');
            if (!button || !popup) {
                return;
            }

            var hoverCloseTimer = null;

            group.addEventListener('mouseenter', function () {
                window.clearTimeout(hoverCloseTimer);
                group.classList.add('is-hovered');
                button.setAttribute('aria-expanded', 'true');
            });

            group.addEventListener('mouseleave', function () {
                window.clearTimeout(hoverCloseTimer);
                hoverCloseTimer = window.setTimeout(function () {
                    group.classList.remove('is-hovered');
                    if (!group.classList.contains('is-open')) {
                        button.setAttribute('aria-expanded', 'false');
                    }
                }, 350);
            });

            button.addEventListener('click', function (event) {
                event.stopPropagation();
                var willOpen = !group.classList.contains('is-open');
                closeAll(group);
                group.classList.toggle('is-open', willOpen);
                button.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            });

            group.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    group.classList.remove('is-open');
                    button.setAttribute('aria-expanded', 'false');
                    button.focus();
                }
            });
        });

        document.addEventListener('click', function (event) {
            if (!root.contains(event.target) || !event.target.closest('[data-rts-mc-popover]')) {
                closeAll();
            }
        });

        var printButton = root.querySelector('[data-rts-mc-print]');
        if (printButton) {
            printButton.addEventListener('click', function () {
                window.print();
            });
        }

        initMap(root);
    }

    function initMap(root) {
        var map = root.querySelector('[data-rts-mc-map]');
        var viewport = root.querySelector('[data-rts-mc-map-viewport]');
        if (!map || !viewport || map.dataset.rtsMapReady === '1') {
            return;
        }
        map.dataset.rtsMapReady = '1';

        var minZoom = 1;
        var maxZoom = 4;
        var zoom = 1;
        var panX = 0;
        var panY = 0;
        var drag = null;
        var overlayPoints = Array.prototype.slice.call(
            viewport.querySelectorAll('.rts-mc-marker, .rts-mc-km-tick')
        ).map(function (element) {
            return {
                element: element,
                x: parseFloat(element.style.getPropertyValue('--rts-x')) || 0,
                y: parseFloat(element.style.getPropertyValue('--rts-y')) || 0
            };
        });
        var finishersOverlay = viewport.querySelector('.rts-mc-finishers');
        if (finishersOverlay) {
            var finishersStyle = window.getComputedStyle(finishersOverlay);
            overlayPoints.push({
                element: finishersOverlay,
                x: map.clientWidth ? (parseFloat(finishersStyle.left) / map.clientWidth) * 100 : 50,
                y: map.clientHeight ? (parseFloat(finishersStyle.top) / map.clientHeight) * 100 : 30
            });
        }

        function clamp(value, minimum, maximum) {
            return Math.min(maximum, Math.max(minimum, value));
        }

        function clampPan() {
            var maxPanX = Math.max(0, (map.clientWidth * zoom - map.clientWidth) / 2);
            var maxPanY = Math.max(0, (map.clientHeight * zoom - map.clientHeight) / 2);
            panX = clamp(panX, -maxPanX, maxPanX);
            panY = clamp(panY, -maxPanY, maxPanY);
        }

        function render() {
            clampPan();
            viewport.style.setProperty('--rts-map-zoom', zoom.toFixed(3));
            viewport.style.setProperty('--rts-map-pan-x', panX.toFixed(1) + 'px');
            viewport.style.setProperty('--rts-map-pan-y', panY.toFixed(1) + 'px');
            overlayPoints.forEach(function (point) {
                point.element.style.left = (
                    map.clientWidth / 2
                    + ((map.clientWidth * point.x / 100) - map.clientWidth / 2) * zoom
                    + panX
                ).toFixed(2) + 'px';
                point.element.style.top = (
                    map.clientHeight / 2
                    + ((map.clientHeight * point.y / 100) - map.clientHeight / 2) * zoom
                    + panY
                ).toFixed(2) + 'px';
            });
            map.classList.toggle('is-zoomed', zoom > minZoom);
        }

        function setZoom(nextZoom, clientX, clientY) {
            var oldZoom = zoom;
            nextZoom = clamp(nextZoom, minZoom, maxZoom);
            if (nextZoom === oldZoom) {
                return;
            }

            var rect = map.getBoundingClientRect();
            var anchorX = typeof clientX === 'number' ? clientX - rect.left - rect.width / 2 : 0;
            var anchorY = typeof clientY === 'number' ? clientY - rect.top - rect.height / 2 : 0;
            var contentX = (anchorX - panX) / oldZoom;
            var contentY = (anchorY - panY) / oldZoom;

            zoom = nextZoom;
            panX = anchorX - contentX * zoom;
            panY = anchorY - contentY * zoom;
            render();
        }

        function resetView() {
            zoom = minZoom;
            panX = 0;
            panY = 0;
            render();
        }

        var zoomIn = root.querySelector('[data-rts-mc-zoom-in]');
        var zoomOut = root.querySelector('[data-rts-mc-zoom-out]');
        var zoomReset = root.querySelector('[data-rts-mc-zoom-reset]');
        if (zoomIn) {
            zoomIn.addEventListener('click', function () { setZoom(zoom + 0.5); });
        }
        if (zoomOut) {
            zoomOut.addEventListener('click', function () { setZoom(zoom - 0.5); });
        }
        if (zoomReset) {
            zoomReset.addEventListener('click', resetView);
        }

        map.addEventListener('wheel', function (event) {
            if (event.target.closest('.rts-mc-popover')) {
                return;
            }
            event.preventDefault();
            setZoom(zoom + (event.deltaY < 0 ? 0.25 : -0.25), event.clientX, event.clientY);
        }, { passive: false });

        map.addEventListener('dblclick', function (event) {
            if (event.target.closest('button, a, .rts-mc-popover')) {
                return;
            }
            event.preventDefault();
            setZoom(zoom + 0.5, event.clientX, event.clientY);
        });

        map.addEventListener('pointerdown', function (event) {
            if (event.button !== 0 || event.target.closest('button, a, .rts-mc-popover')) {
                return;
            }
            drag = {
                pointerId: event.pointerId,
                startX: event.clientX,
                startY: event.clientY,
                panX: panX,
                panY: panY
            };
            map.setPointerCapture(event.pointerId);
            map.classList.add('is-dragging');
        });

        map.addEventListener('pointermove', function (event) {
            if (!drag || drag.pointerId !== event.pointerId) {
                return;
            }
            panX = drag.panX + event.clientX - drag.startX;
            panY = drag.panY + event.clientY - drag.startY;
            render();
        });

        function endDrag(event) {
            if (!drag || drag.pointerId !== event.pointerId) {
                return;
            }
            drag = null;
            map.classList.remove('is-dragging');
            if (map.hasPointerCapture(event.pointerId)) {
                map.releasePointerCapture(event.pointerId);
            }
        }
        map.addEventListener('pointerup', endDrag);
        map.addEventListener('pointercancel', endDrag);

        map.addEventListener('keydown', function (event) {
            if (event.key === '+' || event.key === '=') {
                event.preventDefault();
                setZoom(zoom + 0.5);
            } else if (event.key === '-' || event.key === '_') {
                event.preventDefault();
                setZoom(zoom - 0.5);
            } else if (event.key === '0' || event.key === 'Home') {
                event.preventDefault();
                resetView();
            }
        });

        if (window.ResizeObserver) {
            new ResizeObserver(render).observe(map);
        } else {
            window.addEventListener('resize', render);
        }
        render();
    }

    function boot() {
        document.querySelectorAll('[data-rts-marathon-challenge]').forEach(initChallenge);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
}());
