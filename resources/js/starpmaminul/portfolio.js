const $ = (selector, context = document) => context.querySelector(selector);
const $$ = (selector, context = document) => [...context.querySelectorAll(selector)];

window.addEventListener('load', () => {
    window.setTimeout(() => $('.loader')?.classList.add('done'), 350);
});

const header = $('#siteHeader');
const progressBar = $('.progress span');
const backTop = $('#backTop');

const onScroll = () => {
    const y = window.scrollY;
    header?.classList.toggle('scrolled', y > 30);

    const max = document.documentElement.scrollHeight - window.innerHeight;
    progressBar?.style.setProperty('--progress', `${max ? (y / max) * 100 : 0}%`);

    if (backTop) backTop.style.opacity = y > 500 ? '1' : '.35';
};

window.addEventListener('scroll', onScroll, { passive: true });
onScroll();

window.addEventListener('pointermove', (event) => {
    document.documentElement.style.setProperty('--mx', `${event.clientX}px`);
    document.documentElement.style.setProperty('--my', `${event.clientY}px`);
});

const menuToggle = $('#menuToggle');
const mobileMenu = $('#mobileMenu');

const closeMenu = () => {
    mobileMenu?.classList.remove('open');
    document.body.classList.remove('menu-open');
    menuToggle?.setAttribute('aria-expanded', 'false');
};

menuToggle?.addEventListener('click', () => {
    if (!mobileMenu) return;
    const open = !mobileMenu.classList.contains('open');
    mobileMenu.classList.toggle('open', open);
    document.body.classList.toggle('menu-open', open);
    menuToggle.setAttribute('aria-expanded', String(open));
});

$$('#mobileMenu a').forEach((link) => link.addEventListener('click', closeMenu));

$('#themeToggle')?.addEventListener('click', () => {
    const next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
    document.documentElement.dataset.theme = next;
    try { localStorage.setItem('aminul-theme', next); } catch (_) { /* Storage may be unavailable. */ }
});

try {
    const savedTheme = localStorage.getItem('aminul-theme');
    if (savedTheme) document.documentElement.dataset.theme = savedTheme;
} catch (_) { /* Storage may be unavailable. */ }

const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('visible');
        revealObserver.unobserve(entry.target);
    });
}, { threshold: .13 });

$$('.reveal').forEach((element) => revealObserver.observe(element));

const caseVisual = $('.case-visual');
if (caseVisual) {
    const caseVisualObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) entry.target.classList.add('in-view');
        });
    }, { threshold: .4 });
    caseVisualObserver.observe(caseVisual);
}

const countObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (!entry.isIntersecting) return;

        const element = entry.target;
        const target = Number(element.dataset.target || 0);
        const decimals = Number(element.dataset.decimals || 0);
        const duration = 1250;
        const start = performance.now();

        const tick = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 4);
            const value = target * eased;
            element.textContent = decimals ? value.toFixed(decimals) : Math.round(value).toLocaleString();
            if (progress < 1) requestAnimationFrame(tick);
        };

        requestAnimationFrame(tick);
        countObserver.unobserve(element);
    });
}, { threshold: .7 });

$$('.counter').forEach((element) => countObserver.observe(element));

const sections = $$('main section[id]');
const navLinks = $$('.nav a');
const navObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        navLinks.forEach((link) => link.classList.toggle('active', link.getAttribute('href') === `#${entry.target.id}`));
    });
}, { rootMargin: '-35% 0px -55% 0px' });
sections.forEach((section) => navObserver.observe(section));

const projects = window.portfolioProjects || {};
const drawer = $('#projectDrawer');
const drawerTitle = $('#drawerTitle');
const drawerLabel = $('#drawerLabel');
const drawerIntro = $('#drawerIntro');
const drawerDetails = $('#drawerDetails');

const appendDrawerDetail = ([label, text]) => {
    if (!drawerDetails) return;
    const row = document.createElement('div');
    row.className = 'drawer-detail';

    const labelElement = document.createElement('span');
    labelElement.textContent = label || '';

    const textElement = document.createElement('p');
    textElement.textContent = text || '';

    row.append(labelElement, textElement);
    drawerDetails.appendChild(row);
};

const openProject = (key) => {
    const project = projects[key];
    if (!project || !drawer) return;

    if (drawerLabel) drawerLabel.textContent = project.label || '';
    if (drawerTitle) drawerTitle.textContent = project.title || '';
    if (drawerIntro) drawerIntro.textContent = project.intro || '';
    drawerDetails?.replaceChildren();
    (project.details || []).forEach(appendDrawerDetail);

    drawer.classList.add('open');
    drawer.setAttribute('aria-hidden', 'false');
    document.body.classList.add('menu-open');
    $('.drawer-close')?.focus();
};

const closeDrawer = () => {
    drawer?.classList.remove('open');
    drawer?.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('menu-open');
};

$$('.project-card').forEach((card) => {
    card.addEventListener('click', () => openProject(card.dataset.project));
    card.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        event.preventDefault();
        openProject(card.dataset.project);
    });
});

$$('[data-close-drawer]').forEach((element) => element.addEventListener('click', closeDrawer));
window.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    closeDrawer();
    closeMenu();
});

const testimonials = $$('.testimonial');
let voiceIndex = 0;
let voiceTimer;

const scheduleNextVoice = () => {
    window.clearInterval(voiceTimer);
    if (testimonials.length <= 1) return;
    voiceTimer = window.setInterval(() => showVoice(voiceIndex + 1), 7000);
};

const showVoice = (nextIndex) => {
    if (testimonials.length === 0) return;
    testimonials[voiceIndex]?.classList.remove('active');
    voiceIndex = (nextIndex + testimonials.length) % testimonials.length;
    testimonials[voiceIndex]?.classList.add('active');
    scheduleNextVoice();
};

$('#voicePrev')?.addEventListener('click', () => showVoice(voiceIndex - 1));
$('#voiceNext')?.addEventListener('click', () => showVoice(voiceIndex + 1));
scheduleNextVoice();

backTop?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

const philosophyVisual = $('#philosophyVisual');
if (philosophyVisual) {
    const philosophyNodes = $$('[data-philosophy-node]', philosophyVisual);
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    philosophyNodes.forEach((node) => {
        const index = Number(node.dataset.nodeIndex || 0);
        const count = Math.max(Number(node.dataset.nodeCount || philosophyNodes.length), 1);
        const angle = (-90 + (360 / count) * index) * (Math.PI / 180);
        const radius = 46;
        node.style.left = `${50 + Math.cos(angle) * radius}%`;
        node.style.top = `${50 + Math.sin(angle) * radius}%`;
    });

    const setActiveNode = (node, active) => {
        if (active) {
            philosophyNodes.forEach((item) => item.classList.remove('is-active'));
            node.classList.add('is-active');
        } else {
            node.classList.remove('is-active');
        }
        philosophyVisual.classList.toggle('has-active', philosophyNodes.some((item) => item.classList.contains('is-active')));
    };

    philosophyNodes.forEach((node) => {
        node.addEventListener('pointerenter', () => setActiveNode(node, true));
        node.addEventListener('pointerleave', () => setActiveNode(node, false));
        node.addEventListener('focus', () => setActiveNode(node, true));
        node.addEventListener('blur', () => setActiveNode(node, false));
        node.addEventListener('click', () => setActiveNode(node, !node.classList.contains('is-active')));
    });

    if (!reducedMotion) {
        let frame;
        let targetTiltX = 0;
        let targetTiltY = 0;
        let currentTiltX = 0;
        let currentTiltY = 0;
        let targetParallaxX = 0;
        let targetParallaxY = 0;
        let currentParallaxX = 0;
        let currentParallaxY = 0;

        const animatePhilosophy = () => {
            currentTiltX += (targetTiltX - currentTiltX) * .08;
            currentTiltY += (targetTiltY - currentTiltY) * .08;
            currentParallaxX += (targetParallaxX - currentParallaxX) * .07;
            currentParallaxY += (targetParallaxY - currentParallaxY) * .07;
            philosophyVisual.style.setProperty('--tilt-x', `${currentTiltX.toFixed(2)}deg`);
            philosophyVisual.style.setProperty('--tilt-y', `${currentTiltY.toFixed(2)}deg`);
            philosophyVisual.style.setProperty('--parallax-x', `${currentParallaxX.toFixed(2)}px`);
            philosophyVisual.style.setProperty('--parallax-y', `${currentParallaxY.toFixed(2)}px`);
            frame = requestAnimationFrame(animatePhilosophy);
        };

        frame = requestAnimationFrame(animatePhilosophy);
        philosophyVisual.addEventListener('pointermove', (event) => {
            const rect = philosophyVisual.getBoundingClientRect();
            const x = (event.clientX - rect.left) / rect.width;
            const y = (event.clientY - rect.top) / rect.height;
            targetTiltY = (x - .5) * 8;
            targetTiltX = (.5 - y) * 8;
            targetParallaxX = (x - .5) * 12;
            targetParallaxY = (y - .5) * 12;
            philosophyVisual.style.setProperty('--cursor-x', `${x * 100}%`);
            philosophyVisual.style.setProperty('--cursor-y', `${y * 100}%`);
        });
        philosophyVisual.addEventListener('pointerleave', () => {
            targetTiltX = 0;
            targetTiltY = 0;
            targetParallaxX = 0;
            targetParallaxY = 0;
            philosophyVisual.style.setProperty('--cursor-x', '50%');
            philosophyVisual.style.setProperty('--cursor-y', '50%');
        });
        window.addEventListener('pagehide', () => cancelAnimationFrame(frame));
    }
}
