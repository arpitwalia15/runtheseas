(function () {
    'use strict';

    function initJourney(root) {
        var type = root.querySelector('[data-rts-journey-type]');
        var period = root.querySelector('[data-rts-journey-period]');
        var sort = root.querySelector('[data-rts-journey-sort]');
        var body = root.querySelector('[data-rts-journey-rows]');
        var print = root.querySelector('[data-rts-journey-print]');
        var email = root.querySelector('[data-rts-journey-email]');

        function enhanceSelect(select) {
            if (!select || select.dataset.rtsEnhanced === '1') return;
            select.dataset.rtsEnhanced = '1';

            var custom = document.createElement('div');
            custom.className = 'rts-journey__select';
            var toggle = document.createElement('button');
            toggle.type = 'button';
            toggle.className = 'rts-journey__select-toggle';
            toggle.setAttribute('aria-haspopup', 'listbox');
            toggle.setAttribute('aria-expanded', 'false');
            var value = document.createElement('span');
            value.textContent = select.options[select.selectedIndex].text;
            var arrow = document.createElement('i');
            arrow.setAttribute('aria-hidden', 'true');
            toggle.appendChild(value);
            toggle.appendChild(arrow);

            var menu = document.createElement('div');
            menu.className = 'rts-journey__select-menu';
            menu.setAttribute('role', 'listbox');
            Array.prototype.forEach.call(select.options, function (option) {
                var item = document.createElement('button');
                item.type = 'button';
                item.className = 'rts-journey__select-option';
                item.setAttribute('role', 'option');
                item.dataset.value = option.value;
                item.textContent = option.text;
                if (option.selected) {
                    item.classList.add('is-selected');
                    item.setAttribute('aria-selected', 'true');
                }
                item.addEventListener('click', function () {
                    select.value = item.dataset.value;
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    value.textContent = item.textContent;
                    menu.querySelectorAll('.rts-journey__select-option').forEach(function (choice) {
                        var selected = choice === item;
                        choice.classList.toggle('is-selected', selected);
                        choice.setAttribute('aria-selected', selected ? 'true' : 'false');
                    });
                    custom.classList.remove('is-open');
                    toggle.setAttribute('aria-expanded', 'false');
                    toggle.focus();
                });
                menu.appendChild(item);
            });

            toggle.addEventListener('click', function () {
                root.querySelectorAll('.rts-journey__select.is-open').forEach(function (opened) {
                    if (opened !== custom) {
                        opened.classList.remove('is-open');
                        opened.querySelector('.rts-journey__select-toggle').setAttribute('aria-expanded', 'false');
                    }
                });
                var open = custom.classList.toggle('is-open');
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
            toggle.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    custom.classList.remove('is-open');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });

            custom.appendChild(toggle);
            custom.appendChild(menu);
            select.insertAdjacentElement('afterend', custom);
        }

        function enhanceScrollbar(scrollArea) {
            if (!scrollArea || scrollArea.dataset.rtsScrollbar === '1') return;
            scrollArea.dataset.rtsScrollbar = '1';

            var report = scrollArea.closest('.rts-journey__report');
            if (!report) return;

            var bar = document.createElement('div');
            bar.className = 'rts-journey__scrollbar';
            bar.setAttribute('aria-hidden', 'true');
            var up = document.createElement('button');
            up.type = 'button';
            up.className = 'rts-journey__scroll-button rts-journey__scroll-button--up';
            up.tabIndex = -1;
            var rail = document.createElement('div');
            rail.className = 'rts-journey__scroll-rail';
            var thumb = document.createElement('div');
            thumb.className = 'rts-journey__scroll-thumb';
            var down = document.createElement('button');
            down.type = 'button';
            down.className = 'rts-journey__scroll-button rts-journey__scroll-button--down';
            down.tabIndex = -1;
            rail.appendChild(thumb);
            bar.appendChild(up);
            bar.appendChild(rail);
            bar.appendChild(down);
            report.appendChild(bar);

            function updateThumb() {
                var overflow = scrollArea.scrollHeight > scrollArea.clientHeight + 1;
                bar.classList.toggle('is-hidden', !overflow);
                if (!overflow) return;
                var railHeight = rail.clientHeight;
                // Keep the control visually balanced like the approved report
                // artwork, even when only a small amount of content overflows.
                var proportionalHeight = Math.round(railHeight * scrollArea.clientHeight / scrollArea.scrollHeight);
                var thumbHeight = Math.max(34, Math.min(proportionalHeight, Math.round(railHeight * .58)));
                var travel = Math.max(0, railHeight - thumbHeight);
                var maxScroll = Math.max(1, scrollArea.scrollHeight - scrollArea.clientHeight);
                thumb.style.height = thumbHeight + 'px';
                thumb.style.transform = 'translateY(' + Math.round(travel * scrollArea.scrollTop / maxScroll) + 'px)';
            }

            function step(direction) {
                scrollArea.scrollBy({ top: direction * 42, behavior: 'smooth' });
            }

            up.addEventListener('click', function () { step(-1); });
            down.addEventListener('click', function () { step(1); });
            rail.addEventListener('click', function (event) {
                if (event.target === thumb) return;
                var bounds = rail.getBoundingClientRect();
                scrollArea.scrollBy({
                    top: event.clientY < bounds.top + bounds.height / 2 ? -scrollArea.clientHeight * .8 : scrollArea.clientHeight * .8,
                    behavior: 'smooth'
                });
            });

            thumb.addEventListener('pointerdown', function (event) {
                event.preventDefault();
                thumb.setPointerCapture(event.pointerId);
                var startY = event.clientY;
                var startScroll = scrollArea.scrollTop;
                var railTravel = Math.max(1, rail.clientHeight - thumb.offsetHeight);
                var scrollTravel = Math.max(0, scrollArea.scrollHeight - scrollArea.clientHeight);
                function move(moveEvent) {
                    scrollArea.scrollTop = startScroll + (moveEvent.clientY - startY) * scrollTravel / railTravel;
                }
                function end() {
                    thumb.removeEventListener('pointermove', move);
                    thumb.removeEventListener('pointerup', end);
                    thumb.removeEventListener('pointercancel', end);
                    thumb.classList.remove('is-dragging');
                }
                thumb.classList.add('is-dragging');
                thumb.addEventListener('pointermove', move);
                thumb.addEventListener('pointerup', end);
                thumb.addEventListener('pointercancel', end);
            });

            scrollArea.addEventListener('scroll', updateThumb, { passive: true });
            window.addEventListener('resize', updateThumb);
            if ('ResizeObserver' in window) {
                new ResizeObserver(updateThumb).observe(scrollArea);
            }
            updateThumb();
        }

        function update() {
            if (!body) return;
            var now = Math.floor(Date.now() / 1000);
            var rows = Array.prototype.slice.call(body.querySelectorAll('tr[data-type]'));
            rows.sort(function (a, b) {
                var difference = Number(a.dataset.time) - Number(b.dataset.time);
                return sort && sort.value === 'asc' ? difference : -difference;
            });
            rows.forEach(function (row) {
                var typeMatches = !type || type.value === 'all' || row.dataset.type === type.value;
                var periodMatches = !period || period.value === 'all' || (now - Number(row.dataset.time)) <= Number(period.value) * 86400;
                row.hidden = !(typeMatches && periodMatches);
                body.appendChild(row);
            });
        }

        [type, period, sort].forEach(function (control) {
            if (control) {
                control.addEventListener('change', update);
                enhanceSelect(control);
            }
        });
        enhanceScrollbar(root.querySelector('.rts-journey__table-wrap'));
        if (print) print.addEventListener('click', function () { window.print(); });
        if (email) email.addEventListener('click', function () { openEmailChooser(email); });
    }

    function openEmailChooser(button) {
        var existing = document.querySelector('.rts-journey__email-dialog');
        if (existing) existing.remove();
        var root = button.closest('[data-rts-journey]');
        var dialog = document.createElement('div');
        dialog.className = 'rts-journey__email-dialog';
        dialog.setAttribute('role', 'dialog');
        dialog.setAttribute('aria-modal', 'true');
        dialog.setAttribute('aria-labelledby', 'rts-journey-email-title');
        dialog.innerHTML = '<div class="rts-journey__email-panel">'
            + '<button class="rts-journey__email-close" type="button" aria-label="Close">&times;</button>'
            + '<h2 id="rts-journey-email-title">Email Report</h2>'
            + '<p>The complete Journey report will be attached to the email as an image.</p>'
            + '<form class="rts-journey__email-form">'
            + '<label for="rts-journey-email-recipient">Recipient email</label>'
            + '<input id="rts-journey-email-recipient" type="email" required autocomplete="email" value="' + escapeAttribute(button.dataset.emailDefault || '') + '">'
            + '<button class="rts-journey__email-send" type="submit">Send Report</button>'
            + '<p class="rts-journey__email-status" role="status" aria-live="polite"></p>'
            + '</form>'
            + '</div>';
        document.body.appendChild(dialog);

        var close = function () {
            dialog.remove();
            button.focus();
        };
        dialog.querySelector('.rts-journey__email-close').addEventListener('click', close);
        dialog.addEventListener('click', function (event) {
            if (event.target === dialog) close();
        });
        dialog.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') close();
        });
        var form = dialog.querySelector('.rts-journey__email-form');
        var recipient = dialog.querySelector('#rts-journey-email-recipient');
        var send = dialog.querySelector('.rts-journey__email-send');
        var status = dialog.querySelector('.rts-journey__email-status');
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            if (!form.reportValidity() || !root) return;
            send.disabled = true;
            recipient.disabled = true;
            status.classList.remove('is-error', 'is-success');
            status.textContent = 'Preparing the complete Journey report...';

            captureJourneyReport(root).then(function (blob) {
                status.textContent = 'Sending report...';
                var data = new FormData();
                data.append('action', 'rts_email_journey_report');
                data.append('nonce', button.dataset.emailNonce || '');
                data.append('recipient', recipient.value);
                data.append('report', blob, 'run-the-seas-journey.png');
                return fetch(button.dataset.emailAjax || '', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: data
                }).then(function (response) {
                    return response.json().then(function (payload) {
                        if (!response.ok || !payload.success) {
                            throw new Error(payload && payload.data && payload.data.message ? payload.data.message : 'The report could not be sent.');
                        }
                        return payload.data;
                    });
                });
            }).then(function (result) {
                status.classList.add('is-success');
                status.textContent = result.message || 'Journey report sent.';
                send.disabled = false;
                recipient.disabled = false;
            }).catch(function (error) {
                status.classList.add('is-error');
                status.textContent = error.message || 'The report could not be sent.';
                send.disabled = false;
                recipient.disabled = false;
            });
        });
        recipient.focus();
        recipient.select();
    }

    function escapeAttribute(value) {
        return String(value).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function captureJourneyReport(root) {
        if (!window.domtoimage) {
            return Promise.reject(new Error('The report image exporter did not load. Please refresh and try again.'));
        }

        root.classList.add('is-email-capture');
        var fontReady = document.fonts && document.fonts.ready ? document.fonts.ready : Promise.resolve();
        var images = Array.prototype.slice.call(root.querySelectorAll('img')).map(function (image) {
            if (image.complete) return Promise.resolve();
            return new Promise(function (resolve) {
                image.addEventListener('load', resolve, { once: true });
                image.addEventListener('error', resolve, { once: true });
            });
        });

        return Promise.all([fontReady].concat(images)).then(function () {
            return new Promise(function (resolve) {
                requestAnimationFrame(function () { requestAnimationFrame(resolve); });
            });
        }).then(function () {
            var width = Math.ceil(root.scrollWidth);
            var height = Math.ceil(root.scrollHeight);
            return window.domtoimage.toBlob(root, {
                bgcolor: '#03172b',
                cacheBust: true,
                imagePlaceholder: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADElEQVR42mNk+M/wHwAEAQH/2p8vAAAAAElFTkSuQmCC',
                width: width,
                height: height,
                style: {
                    width: width + 'px',
                    height: height + 'px',
                    maxWidth: 'none',
                    margin: '0',
                    overflow: 'visible'
                },
                filter: function (node) {
                    return !(node.classList && (
                        node.classList.contains('rts-journey__actions') ||
                        node.classList.contains('rts-journey__filters') ||
                        node.classList.contains('rts-journey__scrollbar')
                    ));
                }
            });
        }).finally(function () {
            root.classList.remove('is-email-capture');
        });
    }

    document.addEventListener('click', function (event) {
        document.querySelectorAll('.rts-journey__select.is-open').forEach(function (custom) {
            if (!custom.contains(event.target)) {
                custom.classList.remove('is-open');
                custom.querySelector('.rts-journey__select-toggle').setAttribute('aria-expanded', 'false');
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-rts-journey]').forEach(initJourney);
    });
}());





document.addEventListener('DOMContentLoaded', function () {

    const reports = document.querySelectorAll(
        '.rts-journey__report'
    );

    if (!reports.length) {
        return;
    }

    reports.forEach(function (report) {

        const tableWrap = report.querySelector(
            '.rts-journey__table-wrap'
        );

        if (!tableWrap) {
            return;
        }

        /*
         * Don't create it twice
         */
        if (
            report.querySelector(
                '.rts-journey__horizontal-scrollbar'
            )
        ) {
            return;
        }

        /*
         * Create custom scrollbar
         */
        const scrollbar = document.createElement('div');

        scrollbar.className =
            'rts-journey__horizontal-scrollbar';

        scrollbar.innerHTML = `
            <button
                type="button"
                class="rts-journey__horizontal-scroll-button rts-journey__horizontal-scroll-button--left"
                aria-label="Scroll table left">
            </button>

            <div class="rts-journey__horizontal-scroll-rail">
                <div class="rts-journey__horizontal-scroll-thumb"></div>
            </div>

            <button
                type="button"
                class="rts-journey__horizontal-scroll-button rts-journey__horizontal-scroll-button--right"
                aria-label="Scroll table right">
            </button>
        `;

        report.appendChild(scrollbar);

        const rail = scrollbar.querySelector(
            '.rts-journey__horizontal-scroll-rail'
        );

        const thumb = scrollbar.querySelector(
            '.rts-journey__horizontal-scroll-thumb'
        );

        const leftButton = scrollbar.querySelector(
            '.rts-journey__horizontal-scroll-button--left'
        );

        const rightButton = scrollbar.querySelector(
            '.rts-journey__horizontal-scroll-button--right'
        );


        /*
         * Update thumb size and position
         */
        function updateScrollbar() {

            const visibleWidth = tableWrap.clientWidth;
            const totalWidth = tableWrap.scrollWidth;
            const scrollLeft = tableWrap.scrollLeft;

            if (totalWidth <= visibleWidth + 1) {

                scrollbar.style.display = 'none';

                return;
            }

            scrollbar.style.display = '';

            const railWidth = rail.clientWidth;

            /*
             * Thumb width based on visible area
             */
            let thumbWidth =
                (visibleWidth / totalWidth) * railWidth;

            thumbWidth = Math.max(
                35,
                Math.min(railWidth, thumbWidth)
            );

            thumb.style.width = thumbWidth + 'px';

            /*
             * Maximum movement of thumb
             */
            const maxThumbMove =
                railWidth - thumbWidth;

            const maxScroll =
                totalWidth - visibleWidth;

            const thumbPosition =
                maxScroll > 0
                    ? (scrollLeft / maxScroll) * maxThumbMove
                    : 0;

            thumb.style.transform =
                'translateX(' + thumbPosition + 'px)';
        }


        /*
         * Table -> custom scrollbar
         */
        tableWrap.addEventListener(
            'scroll',
            updateScrollbar,
            {
                passive: true
            }
        );


        /*
         * Left arrow
         */
        leftButton.addEventListener(
            'click',
            function () {

                tableWrap.scrollBy({
                    left: -250,
                    behavior: 'smooth'
                });

            }
        );


        /*
         * Right arrow
         */
        rightButton.addEventListener(
            'click',
            function () {

                tableWrap.scrollBy({
                    left: 250,
                    behavior: 'smooth'
                });

            }
        );


        /*
         * Click directly on rail
         */
        rail.addEventListener(
            'click',
            function (event) {

                if (
                    event.target === thumb
                ) {
                    return;
                }

                const rect =
                    rail.getBoundingClientRect();

                const clickPosition =
                    event.clientX - rect.left;

                const railWidth =
                    rail.clientWidth;

                const thumbWidth =
                    thumb.offsetWidth;

                const maxThumbMove =
                    railWidth - thumbWidth;

                const percentage =
                    Math.max(
                        0,
                        Math.min(
                            1,
                            (clickPosition - thumbWidth / 2) /
                            maxThumbMove
                        )
                    );

                const maxScroll =
                    tableWrap.scrollWidth -
                    tableWrap.clientWidth;

                tableWrap.scrollLeft =
                    percentage * maxScroll;
            }
        );


        /*
         * Drag thumb
         */
        let dragging = false;
        let startX = 0;
        let startScrollLeft = 0;

        thumb.addEventListener(
            'pointerdown',
            function (event) {

                dragging = true;

                startX = event.clientX;

                startScrollLeft =
                    tableWrap.scrollLeft;

                thumb.setPointerCapture(
                    event.pointerId
                );

                event.preventDefault();
            }
        );


        thumb.addEventListener(
            'pointermove',
            function (event) {

                if (!dragging) {
                    return;
                }

                const deltaX =
                    event.clientX - startX;

                const railWidth =
                    rail.clientWidth;

                const thumbWidth =
                    thumb.offsetWidth;

                const maxThumbMove =
                    railWidth - thumbWidth;

                const maxScroll =
                    tableWrap.scrollWidth -
                    tableWrap.clientWidth;

                if (maxThumbMove <= 0) {
                    return;
                }

                const scrollAmount =
                    (deltaX / maxThumbMove) *
                    maxScroll;

                tableWrap.scrollLeft =
                    startScrollLeft +
                    scrollAmount;
            }
        );


        function stopDragging() {
            dragging = false;
        }

        thumb.addEventListener(
            'pointerup',
            stopDragging
        );

        thumb.addEventListener(
            'pointercancel',
            stopDragging
        );

        thumb.addEventListener(
            'lostpointercapture',
            stopDragging
        );


        /*
         * Initial calculation
         */
        updateScrollbar();


        /*
         * Recalculate after resize
         */
        window.addEventListener(
            'resize',
            updateScrollbar
        );


        /*
         * Recalculate after fonts/layout finish
         */
        setTimeout(
            updateScrollbar,
            300
        );

    });

});