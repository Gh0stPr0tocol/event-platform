document.addEventListener('DOMContentLoaded', () => {
    const navLinks = Array.from(document.querySelectorAll('.main-nav .nav-link'));
    const howItWorksLink = document.querySelector('[data-nav-anchor="how-it-works"]');

    function clearAnchorHighlight() {
        if (!howItWorksLink) return;
        howItWorksLink.classList.remove('is-active');
        howItWorksLink.removeAttribute('aria-current');
    }

    function syncHowItWorksHighlight() {
        if (!howItWorksLink) return;

        if (window.location.hash === '#how-it-works') {
            navLinks.forEach((link) => {
                link.classList.remove('is-active');
                link.removeAttribute('aria-current');
            });
            howItWorksLink.classList.add('is-active');
            howItWorksLink.setAttribute('aria-current', 'page');
        } else {
            clearAnchorHighlight();
        }
    }

    if (howItWorksLink) {
        howItWorksLink.addEventListener('click', () => {
            navLinks.forEach((link) => {
                if (link !== howItWorksLink) {
                    link.classList.remove('is-active');
                    link.removeAttribute('aria-current');
                }
            });
            howItWorksLink.classList.add('is-active');
            howItWorksLink.setAttribute('aria-current', 'page');
        });
    }

    window.addEventListener('hashchange', syncHowItWorksHighlight);
    window.addEventListener('popstate', syncHowItWorksHighlight);
    syncHowItWorksHighlight();
});
