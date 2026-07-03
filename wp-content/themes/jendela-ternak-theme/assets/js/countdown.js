/**
 * countdown.js — Flash Sale Countdown Timer
 *
 * Reads `data-end-time` ISO 8601 attribute from .jt-countdown elements
 * and counts down in real time, updating HH:MM:SS units.
 */

(function () {
    'use strict';

    function pad(n) {
        return String(n).padStart(2, '0');
    }

    function updateCountdown(el, endTime) {
        const now   = Date.now();
        const diff  = endTime - now;

        if (diff <= 0) {
            el.closest('.jt-flash-sale')?.remove();
            return false; // signal to stop interval
        }

        const totalSecs = Math.floor(diff / 1000);
        const hours     = Math.floor(totalSecs / 3600);
        const minutes   = Math.floor((totalSecs % 3600) / 60);
        const seconds   = totalSecs % 60;

        const hEl = el.querySelector('[data-jt-hours]');
        const mEl = el.querySelector('[data-jt-minutes]');
        const sEl = el.querySelector('[data-jt-seconds]');

        if (hEl) hEl.textContent = pad(hours);
        if (mEl) mEl.textContent = pad(minutes);
        if (sEl) sEl.textContent = pad(seconds);

        return true;
    }

    function initCountdowns() {
        const countdowns = document.querySelectorAll('.jt-countdown[data-end-time]');

        countdowns.forEach((el) => {
            const rawTime = el.dataset.endTime;
            if (!rawTime) return;

            const endTime = new Date(rawTime).getTime();
            if (isNaN(endTime)) return;

            // Initial render
            const alive = updateCountdown(el, endTime);
            if (!alive) return;

            // Tick every second
            const interval = setInterval(() => {
                const ok = updateCountdown(el, endTime);
                if (!ok) clearInterval(interval);
            }, 1000);
        });
    }

    // Init on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCountdowns);
    } else {
        initCountdowns();
    }
})();
