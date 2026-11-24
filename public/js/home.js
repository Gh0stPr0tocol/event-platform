// Home-page interactions are deliberately kept separate from shared site scripts.
document.addEventListener('DOMContentLoaded', () => {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function initHeadlineRotation() {
        const headlineWord = document.querySelector('[data-rotating-word]');
        if (!headlineWord) return;

        const words = ['learn', 'volunteer', 'play', 'grow', 'connect'];
        const wordDisplay = headlineWord.querySelector('[data-word-display]');
        let currentIndex = 0;

        window.setInterval(() => {
            if (document.hidden) return;

            headlineWord.classList.add('is-changing');
            window.setTimeout(() => {
                currentIndex = (currentIndex + 1) % words.length;
                wordDisplay.textContent = `${words[currentIndex]}.`;
                headlineWord.classList.remove('is-changing');
            }, 180);
        }, 2500);
    }

    function initCounters() {
        const counters = Array.from(document.querySelectorAll('[data-count-up]'));
        const section = document.querySelector('[data-count-section]');
        if (!section || counters.length === 0 || !('IntersectionObserver' in window)) return;

        function animateCounter(counter) {
            const target = Number(counter.dataset.countUp);
            if (!Number.isFinite(target)) return;

            const duration = 1000;
            const startedAt = performance.now();

            function update(now) {
                const progress = Math.min((now - startedAt) / duration, 1);
                const easedProgress = 1 - ((1 - progress) ** 3);
                counter.textContent = Math.round(target * easedProgress).toLocaleString();
                if (progress < 1) requestAnimationFrame(update);
            }

            requestAnimationFrame(update);
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                counters.forEach(animateCounter);
                observer.disconnect();
            });
        }, { threshold: 0.25 });

        observer.observe(section);
    }

    function initCountdown() {
        const countdown = document.querySelector('[data-countdown-target]');
        if (!countdown) return;

        const targetTime = new Date(countdown.dataset.countdownTarget).getTime();
        const units = countdown.querySelector('[data-countdown-units]');
        const startedMessage = countdown.querySelector('[data-countdown-started]');
        const dayOutput = countdown.querySelector('[data-countdown-days]');
        const hourOutput = countdown.querySelector('[data-countdown-hours]');
        const minuteOutput = countdown.querySelector('[data-countdown-minutes]');
        const secondOutput = countdown.querySelector('[data-countdown-seconds]');
        let countdownInterval;

        if (!Number.isFinite(targetTime)) return;

        function updateCountdown() {
            const secondsRemaining = Math.max(0, Math.floor((targetTime - Date.now()) / 1000));
            if (secondsRemaining === 0) {
                window.clearInterval(countdownInterval);
                units.hidden = true;
                startedMessage.hidden = false;
                countdown.setAttribute('aria-label', 'Starting now');
                return;
            }

            const days = Math.floor(secondsRemaining / 86400);
            const hours = Math.floor((secondsRemaining % 86400) / 3600);
            const minutes = Math.floor((secondsRemaining % 3600) / 60);
            const seconds = secondsRemaining % 60;

            dayOutput.textContent = String(days);
            hourOutput.textContent = String(hours).padStart(2, '0');
            minuteOutput.textContent = String(minutes).padStart(2, '0');
            secondOutput.textContent = String(seconds).padStart(2, '0');
            countdown.setAttribute(
                'aria-label',
                `${days} days, ${hours} hours, ${minutes} minutes and ${seconds} seconds until the event starts`
            );
        }

        updateCountdown();
        if (targetTime > Date.now()) countdownInterval = window.setInterval(updateCountdown, 1000);
    }

    function initScrollReveal() {
        const elements = Array.from(document.querySelectorAll('[data-reveal]'));
        if (elements.length === 0 || !('IntersectionObserver' in window)) return;

        document.documentElement.classList.add('motion-ready');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.12 });

        elements.forEach((element) => observer.observe(element));
    }

    initCountdown();
    if (reducedMotion) return;

    initHeadlineRotation();
    initCounters();
    initScrollReveal();
});
