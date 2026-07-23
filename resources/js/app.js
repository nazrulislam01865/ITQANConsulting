const nav = document.getElementById('navlinks');
const menuBtn = document.getElementById('menuBtn');

if (menuBtn && nav) {
  menuBtn.addEventListener('click', () => {
    const open = nav.classList.toggle('open');
    menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    menuBtn.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
  });

  nav.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
      nav.classList.remove('open');
      menuBtn.setAttribute('aria-expanded', 'false');
      menuBtn.setAttribute('aria-label', 'Open menu');
    });
  });
}

const motionToggle = document.getElementById('motionToggle');
if (motionToggle) {
  motionToggle.addEventListener('click', () => {
    document.body.classList.toggle('no-motion');
    const off = document.body.classList.contains('no-motion');
    motionToggle.innerHTML = off ? '<span></span>Motion Off' : '<span></span>Motion On';
    motionToggle.setAttribute('aria-pressed', off ? 'true' : 'false');
  });
}

const revealItems = [...document.querySelectorAll('.reveal')];
const runReveal = () => {
  revealItems.forEach((item) => {
    const rect = item.getBoundingClientRect();
    if (rect.top < window.innerHeight - 60) item.classList.add('visible');
  });
};

if ('IntersectionObserver' in window) {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) entry.target.classList.add('visible');
    });
  }, { threshold: 0.12 });
  revealItems.forEach((item) => observer.observe(item));
} else {
  revealItems.forEach((item) => item.classList.add('visible'));
}
runReveal();
window.addEventListener('load', runReveal);

// FAQ accordions
const faqButtons = document.querySelectorAll('.faq-q');
faqButtons.forEach((button) => {
  const item = button.closest('.faq-item');
  if (item) button.setAttribute('aria-expanded', item.classList.contains('open') ? 'true' : 'false');
  button.addEventListener('click', () => {
    const wrapper = button.closest('.faq-item');
    if (!wrapper) return;
    wrapper.classList.toggle('open');
    button.setAttribute('aria-expanded', wrapper.classList.contains('open') ? 'true' : 'false');
  });
});

// Works filtering
const workTabs = document.querySelectorAll('.tab[data-filter]');
workTabs.forEach((tab) => {
  tab.addEventListener('click', () => {
    document.querySelectorAll('.tab').forEach((item) => {
      item.classList.remove('active');
      item.setAttribute('aria-selected', 'false');
    });
    tab.classList.add('active');
    tab.setAttribute('aria-selected', 'true');

    const filter = tab.dataset.filter;
    document.querySelectorAll('#workGrid .work-card').forEach((card) => {
      const categories = card.dataset.cats || '';
      const show = filter === 'all' || categories.split(' ').includes(filter);
      card.style.display = show ? '' : 'none';
    });
  });
});

// Catalog viewer
const catalogPages = window.ITQAN_CATALOG_PAGES || [];
const leftPage = document.getElementById('leftPage');
const rightPage = document.getElementById('rightPage');
const pageIndicator = document.getElementById('pageIndicator');
const thumbs = document.getElementById('thumbs');
const catalogTitle = document.getElementById('catalogTitle');
const catalogStage = document.getElementById('catalogStage');
const bookWrap = document.getElementById('bookWrap');
const prevPage = document.getElementById('prevPage');
const nextPage = document.getElementById('nextPage');
const fullBtn = document.getElementById('fullBtn');
const muteBtn = document.getElementById('muteBtn');
const thumbToggle = document.getElementById('thumbToggle');
const catalogSinglePageQuery = window.matchMedia ? window.matchMedia('(max-width: 1000px)') : null;
let currentCatalogPage = 0;
let catalogMuted = true;

function isCatalogSinglePageMode() {
  return catalogSinglePageQuery ? catalogSinglePageQuery.matches : window.innerWidth <= 1000;
}

function escapeHtml(value) {
  const div = document.createElement('div');
  div.textContent = value ?? '';
  return div.innerHTML;
}

function catalogPageHtml(page, pageNo) {
  if (!page) return '';
  const imageUrl = page.image_url || '';
  const videoUrl = page.video_url || '';
  const thumbnailUrl = page.thumbnail_url || '';
  let media = `<div class="page-graphic"></div>`;

  if (page.type === 'video') {
    if (videoUrl) {
      const posterAttr = thumbnailUrl ? ` poster="${escapeHtml(thumbnailUrl)}"` : '';
      media = `<div class="page-graphic has-media"><video class="page-media-video" controls muted playsinline${posterAttr}><source src="${escapeHtml(videoUrl)}"></video></div>`;
    } else if (thumbnailUrl || imageUrl) {
      media = `<div class="page-graphic has-media video-thumb"><img class="page-media-image" src="${escapeHtml(thumbnailUrl || imageUrl)}" alt="${escapeHtml(page.title)} thumbnail"><span class="play overlay-play">▶</span></div>`;
    } else {
      media = `<div class="page-graphic"><div class="video-placeholder"><div><div class="play">▶</div><p style="color:white;text-align:center;margin:14px 0 0">Video page ready</p></div></div></div>`;
    }
  } else if (imageUrl) {
    media = `<div class="page-graphic has-media"><img class="page-media-image" src="${escapeHtml(imageUrl)}" alt="${escapeHtml(page.title)} image"></div>`;
  }

  return `<div class="page-kicker">${escapeHtml(page.kicker)}</div><h3>${escapeHtml(page.title)}</h3><p>${escapeHtml(page.body)}</p>${media}<div class="page-no">${pageNo}</div>`;
}

function renderCatalog(animate = true) {
  if (!leftPage || !rightPage || !pageIndicator || !thumbs || !catalogTitle || catalogPages.length === 0) return;

  const singlePage = isCatalogSinglePageMode();
  const leftIndex = singlePage ? currentCatalogPage : Math.max(0, currentCatalogPage - (currentCatalogPage % 2));
  const rightIndex = singlePage ? currentCatalogPage : Math.min(catalogPages.length - 1, leftIndex + 1);

  if (singlePage) {
    leftPage.innerHTML = '';
    leftPage.setAttribute('aria-hidden', 'true');
    rightPage.innerHTML = catalogPageHtml(catalogPages[currentCatalogPage], currentCatalogPage + 1);
  } else {
    leftPage.removeAttribute('aria-hidden');
    leftPage.innerHTML = catalogPageHtml(catalogPages[leftIndex], leftIndex + 1);
    rightPage.innerHTML = rightIndex === leftIndex
      ? ''
      : catalogPageHtml(catalogPages[rightIndex], rightIndex + 1);
  }

  catalogTitle.textContent = catalogPages[currentCatalogPage]?.title || '';
  pageIndicator.textContent = `Page ${currentCatalogPage + 1} of ${catalogPages.length}`;
  [...thumbs.children].forEach((thumb, index) => thumb.classList.toggle('active', index === currentCatalogPage));

  if (animate && !document.body.classList.contains('no-motion')) {
    rightPage.classList.remove('turning');
    void rightPage.offsetWidth;
    rightPage.classList.add('turning');
  }
}

function goCatalogPage(direction) {
  currentCatalogPage = Math.max(0, Math.min(catalogPages.length - 1, currentCatalogPage + direction));
  renderCatalog(true);
}

if (catalogPages.length && thumbs) {
  catalogPages.forEach((page, index) => {
    const thumb = document.createElement('button');
    thumb.className = 'thumb';
    thumb.type = 'button';
    thumb.textContent = String(index + 1).padStart(2, '0');
    thumb.title = page.title;
    thumb.setAttribute('aria-label', `Open catalog page ${index + 1}: ${page.title}`);
    thumb.addEventListener('click', () => {
      currentCatalogPage = index;
      renderCatalog(true);
    });
    thumbs.appendChild(thumb);
  });
}

if (prevPage) prevPage.addEventListener('click', () => goCatalogPage(-1));
if (nextPage) nextPage.addEventListener('click', () => goCatalogPage(1));
if (catalogSinglePageQuery) {
  catalogSinglePageQuery.addEventListener('change', () => renderCatalog(false));
}
if (fullBtn && catalogStage) fullBtn.addEventListener('click', () => catalogStage.classList.toggle('fullscreen'));
if (muteBtn) {
  muteBtn.addEventListener('click', () => {
    catalogMuted = !catalogMuted;
    muteBtn.textContent = catalogMuted ? 'Mute' : 'Unmute';
  });
}
if (thumbToggle && thumbs) {
  thumbToggle.addEventListener('click', () => {
    thumbs.style.display = thumbs.style.display === 'none' ? 'flex' : 'none';
  });
}

let touchStart = 0;
if (bookWrap) {
  bookWrap.addEventListener('touchstart', (event) => {
    touchStart = event.touches[0].clientX;
  }, { passive: true });

  bookWrap.addEventListener('touchend', (event) => {
    const dx = event.changedTouches[0].clientX - touchStart;
    if (Math.abs(dx) > 45) goCatalogPage(dx < 0 ? 1 : -1);
  }, { passive: true });
}
renderCatalog(false);

// Floating back-to-top button
const backToTop = document.getElementById('backToTop');
if (backToTop) {
  const toggleBackToTop = () => {
    const show = window.scrollY > 360;
    backToTop.hidden = false;
    backToTop.classList.toggle('is-visible', show);
    backToTop.setAttribute('aria-hidden', show ? 'false' : 'true');
    backToTop.tabIndex = show ? 0 : -1;
  };

  backToTop.addEventListener('click', () => {
    const reducedMotion = document.body.classList.contains('no-motion')
      || window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    window.scrollTo({ top: 0, behavior: reducedMotion ? 'auto' : 'smooth' });
  });

  toggleBackToTop();
  window.addEventListener('scroll', toggleBackToTop, { passive: true });
}


// Shared banner: the reference template's dark background, moving connected
// particles, scroll organisation and mouse-following highlight on every page.
const interactiveBanners = [...document.querySelectorAll('[data-itqan-banner]')];
const reducedBannerMotion = window.matchMedia
  ? window.matchMedia('(prefers-reduced-motion: reduce)')
  : { matches: false };

interactiveBanners.forEach((banner) => {
  const canvas = banner.querySelector('.hero-particle-canvas');
  const context = canvas?.getContext('2d');
  if (!canvas || !context) return;

  let width = 1;
  let height = 1;
  let particles = [];
  let visible = true;
  let pointer = { x: 0, y: 0, active: false };

  const motionIsEnabled = () => (
    !document.body.classList.contains('no-motion')
    && !reducedBannerMotion.matches
  );

  const createParticles = () => {
    const count = width < 700 ? 46 : 90;
    const columns = width < 700 ? 8 : 12;
    const rows = Math.ceil(count / columns);

    particles = Array.from({ length: count }, (_, index) => {
      const column = index % columns;
      const row = Math.floor(index / columns);
      const gridX = ((column + 0.7) / columns) * width;
      const gridY = ((row + 1) / rows) * height;
      const angle = Math.random() * Math.PI * 2;
      const speed = 0.08 + (Math.random() * 0.18);

      return {
        gridX,
        gridY,
        x: gridX + ((Math.random() - 0.5) * Math.min(240, width * 0.22)),
        y: gridY + ((Math.random() - 0.5) * Math.min(180, height * 0.26)),
        vx: Math.cos(angle) * speed,
        vy: Math.sin(angle) * speed,
        seed: Math.random() * 10,
        radius: 0.9 + (Math.random() * 0.75),
      };
    });
  };

  const resize = () => {
    const rect = banner.getBoundingClientRect();
    width = Math.max(1, Math.round(rect.width));
    height = Math.max(1, Math.round(banner.offsetHeight));
    const pixelRatio = Math.min(window.devicePixelRatio || 1, 2);

    canvas.width = Math.round(width * pixelRatio);
    canvas.height = Math.round(height * pixelRatio);
    canvas.style.width = `${width}px`;
    canvas.style.height = `${height}px`;
    context.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
    createParticles();
  };

  const bannerProgress = () => {
    const rect = banner.getBoundingClientRect();
    return Math.min(1, Math.max(0, -rect.top / Math.max(1, rect.height * 0.66)));
  };

  const draw = (time = 0) => {
    if (!visible || document.hidden) {
      window.requestAnimationFrame(draw);
      return;
    }

    const motion = motionIsEnabled();
    const organise = bannerProgress();
    context.clearRect(0, 0, width, height);

    particles.forEach((particle, index) => {
      const wave = motion ? Math.sin((time * 0.00045) + particle.seed) * 5 : 0;

      if (motion) {
        particle.x += particle.vx;
        particle.y += particle.vy;
      }

      if (particle.x < -20 || particle.x > width + 20) particle.vx *= -1;
      if (particle.y < -20 || particle.y > height + 20) particle.vy *= -1;

      let x = (particle.x * (1 - organise)) + (particle.gridX * organise);
      let y = ((particle.y + wave) * (1 - organise)) + (particle.gridY * organise);
      let pointerStrength = 0;

      if (pointer.active) {
        const deltaX = x - pointer.x;
        const deltaY = y - pointer.y;
        const distance = Math.hypot(deltaX, deltaY);
        const radius = width < 700 ? 135 : 195;
        pointerStrength = Math.max(0, 1 - (distance / radius));

        if (motion && distance > 0 && pointerStrength > 0) {
          const push = pointerStrength * (width < 700 ? 10 : 18);
          x += (deltaX / distance) * push;
          y += (deltaY / distance) * push;
        }
      }

      particle.drawX = x;
      particle.drawY = y;
      particle.pointerStrength = pointerStrength;
      particle.index = index;
    });

    const connectionDistance = width < 700 ? 100 : 120;
    for (let firstIndex = 0; firstIndex < particles.length; firstIndex += 1) {
      for (let secondIndex = firstIndex + 1; secondIndex < particles.length; secondIndex += 1) {
        const first = particles[firstIndex];
        const second = particles[secondIndex];
        const distance = Math.hypot(first.drawX - second.drawX, first.drawY - second.drawY);
        if (distance >= connectionDistance) continue;

        const hover = Math.max(first.pointerStrength, second.pointerStrength);
        const alpha = (1 - (distance / connectionDistance)) * (0.08 + (0.14 * organise) + (hover * 0.28));
        context.strokeStyle = `rgba(105,212,255,${alpha})`;
        context.lineWidth = 0.7 + (hover * 0.55);
        context.beginPath();
        context.moveTo(first.drawX, first.drawY);
        context.lineTo(second.drawX, second.drawY);
        context.stroke();
      }
    }

    particles.forEach((particle) => {
      const hover = particle.pointerStrength;
      const radius = particle.radius + (hover * 1.6);
      const alpha = Math.min(1, 0.54 + (hover * 0.42));
      context.shadowColor = `rgba(105,212,255,${0.62 + (hover * 0.32)})`;
      context.shadowBlur = 5 + (hover * 17);
      context.fillStyle = `rgba(105,212,255,${alpha})`;
      context.beginPath();
      context.arc(particle.drawX, particle.drawY, radius, 0, Math.PI * 2);
      context.fill();
    });

    context.shadowBlur = 0;
    window.requestAnimationFrame(draw);
  };

  const setPointerPosition = (event) => {
    const rect = banner.getBoundingClientRect();
    pointer = {
      x: Math.max(0, Math.min(rect.width, event.clientX - rect.left)),
      y: Math.max(0, Math.min(rect.height, event.clientY - rect.top)),
      active: true,
    };

    banner.style.setProperty('--banner-mx', `${(pointer.x / rect.width) * 100}%`);
    banner.style.setProperty('--banner-my', `${(pointer.y / rect.height) * 100}%`);
  };

  banner.addEventListener('pointermove', setPointerPosition, { passive: true });
  banner.addEventListener('pointerenter', setPointerPosition, { passive: true });
  banner.addEventListener('pointerleave', () => { pointer.active = false; }, { passive: true });

  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.target === banner) visible = entry.isIntersecting;
      });
    }, { rootMargin: '100px 0px' });
    observer.observe(banner);
  }

  if ('ResizeObserver' in window) {
    const observer = new ResizeObserver(resize);
    observer.observe(banner);
  } else {
    window.addEventListener('resize', resize, { passive: true });
  }

  resize();
  window.requestAnimationFrame(draw);
});


// Template page progress bar.
const scrollProgress = document.getElementById('scrollProgress');
if (scrollProgress) {
  const progressFill = scrollProgress.querySelector('span');
  let progressFrame = 0;

  const updateScrollProgress = () => {
    progressFrame = 0;
    const scrollable = document.documentElement.scrollHeight - window.innerHeight;
    const progress = scrollable > 0 ? Math.min(1, Math.max(0, window.scrollY / scrollable)) : 0;
    if (progressFill) progressFill.style.transform = `scaleX(${progress})`;
  };

  const requestProgressUpdate = () => {
    if (progressFrame) return;
    progressFrame = window.requestAnimationFrame(updateScrollProgress);
  };

  window.addEventListener('scroll', requestProgressUpdate, { passive: true });
  window.addEventListener('resize', requestProgressUpdate);
  window.addEventListener('load', updateScrollProgress);
  updateScrollProgress();
}

// Interactive clarity check copied from the supplied template behaviour.
document.querySelectorAll('[data-clarity-check]').forEach((section) => {
  const dataElement = section.querySelector('[data-clarity-data]');
  const buttons = [...section.querySelectorAll('[data-clarity-index]')];
  const title = section.querySelector('[data-clarity-title]');
  const summary = section.querySelector('[data-clarity-summary]');
  const services = section.querySelector('[data-clarity-services]');

  if (!dataElement || !title || !summary || !services || buttons.length === 0) return;

  let items = [];
  try {
    items = JSON.parse(dataElement.textContent || '[]');
  } catch (error) {
    console.warn('Unable to load clarity check content.', error);
    return;
  }

  const selectItem = (index, focusResult = false) => {
    const item = items[index];
    if (!item) return;

    buttons.forEach((button, buttonIndex) => {
      const active = buttonIndex === index;
      button.classList.toggle('active', active);
      button.setAttribute('aria-pressed', active ? 'true' : 'false');
    });

    title.textContent = item.problem || '';
    summary.textContent = item.summary || '';
    services.replaceChildren();

    const startingPoints = Array.isArray(item.services) ? item.services : [];
    startingPoints.forEach((startingPoint) => {
      const row = document.createElement('div');
      row.className = 'template-result-item';
      row.textContent = String(startingPoint);
      services.appendChild(row);
    });

    if (!document.body.classList.contains('no-motion') && typeof title.animate === 'function') {
      const result = title.closest('.template-diagnostic-result');
      result?.animate(
        [
          { opacity: 0.72, transform: 'translateY(8px)' },
          { opacity: 1, transform: 'translateY(0)' },
        ],
        { duration: 260, easing: 'cubic-bezier(.2,.8,.2,1)' },
      );
    }

    if (focusResult && window.innerWidth < 721) {
      title.focus?.({ preventScroll: true });
    }
  };

  buttons.forEach((button) => {
    button.addEventListener('click', () => {
      selectItem(Number.parseInt(button.dataset.clarityIndex || '0', 10), true);
    });
  });
});

// Why ITQAN story cards reproduce the reference transformation sequence.
document.querySelectorAll('[data-why-itqan]').forEach((section) => {
  const stage = section.querySelector('[data-why-stage]');
  const stageLabel = section.querySelector('[data-why-stage-label]');
  const stories = [...section.querySelectorAll('[data-why-story]')];

  if (!stage || !stageLabel || stories.length === 0) return;

  let stageTimer = null;

  const activateStory = (story) => {
    stories.forEach((item) => item.classList.toggle('active', item === story));
    stageLabel.textContent = story.dataset.stageLabel || 'A clearer way of working';

    if (stageTimer) window.clearTimeout(stageTimer);

    // Restart the messy-to-clear transition for every newly active story.
    stage.classList.remove('clear-state');
    if (!document.body.classList.contains('no-motion')) void stage.offsetWidth;
    stage.classList.add('clear-state');

    stageTimer = window.setTimeout(() => {
      stage.classList.remove('clear-state');
    }, 1900);
  };

  stories.forEach((story) => {
    story.addEventListener('mouseenter', () => activateStory(story));
    story.addEventListener('focus', () => activateStory(story));
    story.addEventListener('click', () => activateStory(story));
  });

  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      const visible = entries
        .filter((entry) => entry.isIntersecting)
        .sort((first, second) => second.intersectionRatio - first.intersectionRatio)[0];

      if (visible) activateStory(visible.target);
    }, { threshold: 0.55, rootMargin: '-10% 0px -20%' });

    stories.forEach((story) => observer.observe(story));
  } else {
    activateStory(stories[0]);
  }
});

// Services explorer: faithful sticky menu, stacked panels, smooth navigation,
// active-tab synchronization, keyboard support, and mobile tab positioning.
document.querySelectorAll('[data-services-explorer]').forEach((section) => {
  const menu = section.querySelector('[data-service-menu]');
  const tabs = [...section.querySelectorAll('[data-service-tab]')];
  const panels = [...section.querySelectorAll('[data-service-panel]')];

  if (!menu || tabs.length === 0 || panels.length === 0) return;

  const reducedMotion = () => (
    document.body.classList.contains('no-motion')
    || window.matchMedia('(prefers-reduced-motion: reduce)').matches
  );

  const panelForTab = (tab) => {
    const id = tab.dataset.serviceTarget || '';
    return panels.find((panel) => panel.id === id) || null;
  };

  const tabForPanel = (panel) => tabs.find((tab) => panelForTab(tab) === panel) || null;
  let programmaticPanel = null;
  let programmaticLockUntil = 0;
  let scrollFrame = 0;

  const setActive = (tab, centerMobileTab = false) => {
    const targetPanel = panelForTab(tab);
    if (!targetPanel) return;

    tabs.forEach((item) => {
      const active = item === tab;
      item.classList.toggle('active', active);
      item.setAttribute('aria-selected', active ? 'true' : 'false');
      item.tabIndex = active ? 0 : -1;
    });

    panels.forEach((panel) => panel.classList.toggle('active', panel === targetPanel));

    if (centerMobileTab && window.innerWidth <= 720) {
      // Keep the horizontal service selector synchronized without calling
      // scrollIntoView(). The latter can also move the page vertically on
      // mobile browsers and make the section feel locked while swiping.
      const targetLeft = tab.offsetLeft - ((menu.clientWidth - tab.offsetWidth) / 2);
      menu.scrollTo({
        left: Math.max(0, targetLeft),
        behavior: reducedMotion() ? 'auto' : 'smooth',
      });
    }
  };

  const scrollToPanel = (tab) => {
    const panel = panelForTab(tab);
    if (!panel) return;

    setActive(tab, true);
    programmaticPanel = panel;
    programmaticLockUntil = performance.now() + (reducedMotion() ? 120 : 1100);

    panel.scrollIntoView({
      behavior: reducedMotion() ? 'auto' : 'smooth',
      block: window.innerWidth <= 1020 ? 'start' : 'center',
      inline: 'nearest',
    });
  };

  tabs.forEach((tab, index) => {
    tab.addEventListener('click', () => scrollToPanel(tab));

    tab.addEventListener('keydown', (event) => {
      let nextIndex = null;

      if (event.key === 'ArrowDown' || event.key === 'ArrowRight') nextIndex = (index + 1) % tabs.length;
      if (event.key === 'ArrowUp' || event.key === 'ArrowLeft') nextIndex = (index - 1 + tabs.length) % tabs.length;
      if (event.key === 'Home') nextIndex = 0;
      if (event.key === 'End') nextIndex = tabs.length - 1;

      if (nextIndex === null) return;

      event.preventDefault();
      tabs[nextIndex].focus({ preventScroll: true });
      scrollToPanel(tabs[nextIndex]);
    });
  });

  const updateFromScroll = () => {
    scrollFrame = 0;
    const now = performance.now();

    if (programmaticPanel && now < programmaticLockUntil) {
      const rect = programmaticPanel.getBoundingClientRect();
      const target = window.innerWidth <= 1020 ? 120 : window.innerHeight / 2;
      const current = window.innerWidth <= 1020 ? rect.top : rect.top + (rect.height / 2);

      if (Math.abs(current - target) < 90) {
        programmaticPanel = null;
        programmaticLockUntil = 0;
      } else {
        return;
      }
    }

    if (programmaticPanel && now >= programmaticLockUntil) {
      programmaticPanel = null;
      programmaticLockUntil = 0;
    }

    const sectionRect = section.getBoundingClientRect();
    if (sectionRect.bottom < 0 || sectionRect.top > window.innerHeight) return;

    const viewportTarget = window.innerWidth <= 1020 ? Math.min(190, window.innerHeight * 0.28) : window.innerHeight * 0.5;
    const nearestPanel = panels
      .map((panel) => {
        const rect = panel.getBoundingClientRect();
        const point = window.innerWidth <= 1020 ? rect.top : rect.top + (rect.height / 2);
        return { panel, distance: Math.abs(point - viewportTarget) };
      })
      .sort((first, second) => first.distance - second.distance)[0]?.panel;

    const matchingTab = nearestPanel ? tabForPanel(nearestPanel) : null;
    if (matchingTab) setActive(matchingTab, true);
  };

  const requestScrollUpdate = () => {
    if (scrollFrame) return;
    scrollFrame = window.requestAnimationFrame(updateFromScroll);
  };

  window.addEventListener('scroll', requestScrollUpdate, { passive: true });
  window.addEventListener('resize', requestScrollUpdate, { passive: true });

  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      if (programmaticPanel && performance.now() < programmaticLockUntil) return;

      const visible = entries
        .filter((entry) => entry.isIntersecting)
        .sort((first, second) => second.intersectionRatio - first.intersectionRatio)[0];

      const matchingTab = visible ? tabForPanel(visible.target) : null;
      if (matchingTab) setActive(matchingTab, true);
    }, {
      threshold: [0.2, 0.35, 0.5, 0.65, 0.8],
      rootMargin: '-12% 0px -28%',
    });

    panels.forEach((panel) => observer.observe(panel));
  }

  setActive(tabs.find((tab) => tab.classList.contains('active')) || tabs[0]);
  updateFromScroll();
});

// Our Way of Working: the template path fills as the visitor moves through
// the section and each process node becomes active in sequence.
document.querySelectorAll('[data-process-section]').forEach((section) => {
  const shell = section.querySelector('[data-process-shell]');
  const progressPath = section.querySelector('[data-process-progress]');
  const steps = [...section.querySelectorAll('[data-process-step]')];

  if (!shell || !progressPath || steps.length === 0) return;

  let processFrame = 0;

  const updateProcess = () => {
    processFrame = 0;
    const rect = shell.getBoundingClientRect();
    const progress = Math.min(1, Math.max(0, (
      window.innerHeight - rect.top
    ) / Math.max(1, window.innerHeight + (rect.height * 0.42))));

    progressPath.style.strokeDashoffset = String(1 - progress);

    steps.forEach((step, index) => {
      const threshold = index / Math.max(1, steps.length - 0.2);
      step.classList.toggle('active', progress > threshold);
    });
  };

  const requestProcessUpdate = () => {
    if (processFrame) return;
    processFrame = window.requestAnimationFrame(updateProcess);
  };

  window.addEventListener('scroll', requestProcessUpdate, { passive: true });
  window.addEventListener('resize', requestProcessUpdate, { passive: true });
  window.addEventListener('load', updateProcess);
  updateProcess();
});

// Client Words: template carousel with autoplay, pause states, controls, dots,
// keyboard navigation, and touch swipe support.
document.querySelectorAll('[data-testimonial-slider]').forEach((section) => {
  const shell = section.querySelector('[data-testimonial-shell]');
  const track = section.querySelector('[data-testimonial-track]');
  const slides = [...section.querySelectorAll('[data-testimonial-slide]')];
  const dotsWrap = section.querySelector('[data-testimonial-dots]');
  const previousButton = section.querySelector('[data-testimonial-prev]');
  const nextButton = section.querySelector('[data-testimonial-next]');
  const progressBar = section.querySelector('[data-testimonial-progress]');

  if (!shell || !track || slides.length === 0) return;

  const intervalMs = 6000;
  const prefersReducedMotion = () => (
    document.body.classList.contains('no-motion')
    || window.matchMedia('(prefers-reduced-motion: reduce)').matches
  );

  let currentIndex = 0;
  let timer = 0;
  let touchStartX = 0;
  let pointerPaused = false;
  let focusPaused = false;
  let touchPaused = false;
  let sectionVisible = true;

  const dots = slides.map((_, index) => {
    if (!dotsWrap) return null;

    const dot = document.createElement('button');
    dot.type = 'button';
    dot.className = 'template-slider-dot';
    dot.setAttribute('aria-label', `Go to testimonial ${index + 1}`);
    dot.addEventListener('click', () => goTo(index, true));
    dotsWrap.appendChild(dot);
    return dot;
  }).filter(Boolean);

  const isPaused = () => (
    pointerPaused
    || focusPaused
    || touchPaused
    || !sectionVisible
    || document.hidden
    || prefersReducedMotion()
  );

  const restartProgress = () => {
    if (!progressBar) return;

    progressBar.style.animation = 'none';
    progressBar.style.width = '0';
    void progressBar.offsetWidth;

    if (prefersReducedMotion()) {
      progressBar.style.width = '100%';
      return;
    }

    progressBar.style.animation = `templateTestimonialProgress ${intervalMs}ms linear infinite`;
  };

  const updatePauseClass = () => {
    shell.classList.toggle('paused', isPaused());
  };

  const goTo = (nextIndex, userInitiated = false) => {
    currentIndex = (nextIndex + slides.length) % slides.length;
    track.style.transform = `translateX(-${currentIndex * 100}%)`;

    slides.forEach((slide, index) => {
      slide.setAttribute('aria-hidden', index === currentIndex ? 'false' : 'true');
    });

    dots.forEach((dot, index) => {
      const active = index === currentIndex;
      dot.classList.toggle('active', active);
      dot.setAttribute('aria-current', active ? 'true' : 'false');
    });

    if (userInitiated) {
      restartProgress();
      restartTimer();
    }
  };

  const restartTimer = () => {
    window.clearInterval(timer);

    if (slides.length < 2) return;

    timer = window.setInterval(() => {
      updatePauseClass();
      if (!isPaused()) goTo(currentIndex + 1);
    }, intervalMs);
  };

  previousButton?.addEventListener('click', () => goTo(currentIndex - 1, true));
  nextButton?.addEventListener('click', () => goTo(currentIndex + 1, true));

  shell.addEventListener('keydown', (event) => {
    if (event.key === 'ArrowLeft') {
      event.preventDefault();
      goTo(currentIndex - 1, true);
    }

    if (event.key === 'ArrowRight') {
      event.preventDefault();
      goTo(currentIndex + 1, true);
    }
  });

  shell.addEventListener('pointerenter', () => {
    pointerPaused = true;
    updatePauseClass();
  });

  shell.addEventListener('pointerleave', () => {
    pointerPaused = false;
    updatePauseClass();
  });

  shell.addEventListener('focusin', () => {
    focusPaused = true;
    updatePauseClass();
  });

  shell.addEventListener('focusout', (event) => {
    if (shell.contains(event.relatedTarget)) return;
    focusPaused = false;
    updatePauseClass();
  });

  shell.addEventListener('touchstart', (event) => {
    touchPaused = true;
    touchStartX = event.touches[0]?.clientX ?? 0;
    updatePauseClass();
  }, { passive: true });

  shell.addEventListener('touchend', (event) => {
    const touchEndX = event.changedTouches[0]?.clientX ?? touchStartX;
    const difference = touchEndX - touchStartX;

    if (Math.abs(difference) > 45) {
      goTo(currentIndex + (difference < 0 ? 1 : -1), true);
    }

    touchPaused = false;
    updatePauseClass();
  }, { passive: true });

  document.addEventListener('visibilitychange', updatePauseClass);

  if ('IntersectionObserver' in window) {
    const visibilityObserver = new IntersectionObserver((entries) => {
      sectionVisible = entries.some((entry) => entry.isIntersecting);
      updatePauseClass();
    }, { threshold: 0.15 });

    visibilityObserver.observe(section);
  }

  shell.tabIndex = shell.tabIndex >= 0 ? shell.tabIndex : 0;
  goTo(0);
  restartProgress();
  restartTimer();
  updatePauseClass();
});

// How We Think: one reliable active principle at a time.
// The active card follows the viewport, while hover/click/focus temporarily
// takes priority so the counter never disagrees with the highlighted card.
document.querySelectorAll('[data-values-section]').forEach((section) => {
  const number = section.querySelector('[data-value-number]');
  const cards = [...section.querySelectorAll('[data-value-card]')];

  if (!number || cards.length === 0) return;

  let activeCard = null;
  let interactionLockUntil = 0;
  let numberTimer = 0;
  let scrollFrame = 0;

  const reducedMotion = () => (
    document.body.classList.contains('no-motion')
    || window.matchMedia('(prefers-reduced-motion: reduce)').matches
  );

  const updateNumber = (nextValue) => {
    if (number.textContent.trim() === nextValue) return;

    window.clearTimeout(numberTimer);

    if (reducedMotion()) {
      number.textContent = nextValue;
      number.classList.remove('changing');
      return;
    }

    number.classList.add('changing');
    numberTimer = window.setTimeout(() => {
      number.textContent = nextValue;
      number.classList.remove('changing');
    }, 120);
  };

  const setActive = (card, userInitiated = false) => {
    if (!card) return;

    activeCard = card;
    cards.forEach((item) => {
      const active = item === card;
      item.classList.toggle('active', active);
      item.setAttribute('aria-current', active ? 'true' : 'false');
    });

    updateNumber(card.dataset.value || '01');

    if (userInitiated) {
      // Keep the selection stable briefly, but never force-scroll the page on
      // phones or tablets. Forced scrolling made normal touch swipes feel locked.
      interactionLockUntil = performance.now() + 900;
    }
  };

  const updateFromViewport = () => {
    scrollFrame = 0;
    if (performance.now() < interactionLockUntil) return;

    const sectionRect = section.getBoundingClientRect();
    if (sectionRect.bottom < 0 || sectionRect.top > window.innerHeight) return;

    const targetY = window.innerHeight * (window.innerWidth <= 1020 ? 0.38 : 0.48);
    const nearest = cards
      .map((card) => {
        const rect = card.getBoundingClientRect();
        const center = rect.top + (rect.height / 2);
        return { card, distance: Math.abs(center - targetY) };
      })
      .sort((a, b) => a.distance - b.distance)[0]?.card;

    if (nearest && nearest !== activeCard) setActive(nearest);
  };

  const requestViewportUpdate = () => {
    if (scrollFrame) return;
    scrollFrame = window.requestAnimationFrame(updateFromViewport);
  };

  cards.forEach((card) => {
    card.setAttribute('role', 'button');
    card.setAttribute('aria-label', `Select principle ${card.dataset.value || ''}`.trim());

    card.addEventListener('mouseenter', () => {
      if (window.matchMedia('(hover: hover)').matches) setActive(card, true);
    });
    card.addEventListener('focus', () => setActive(card, true));
    card.addEventListener('click', () => setActive(card, true));
    card.addEventListener('keydown', (event) => {
      const index = cards.indexOf(card);
      let nextIndex = null;

      if (event.key === 'ArrowDown' || event.key === 'ArrowRight') nextIndex = Math.min(cards.length - 1, index + 1);
      if (event.key === 'ArrowUp' || event.key === 'ArrowLeft') nextIndex = Math.max(0, index - 1);
      if (event.key === 'Home') nextIndex = 0;
      if (event.key === 'End') nextIndex = cards.length - 1;
      if (nextIndex === null) return;

      event.preventDefault();
      cards[nextIndex].focus({ preventScroll: true });
      setActive(cards[nextIndex], true);
    });
  });

  window.addEventListener('scroll', requestViewportUpdate, { passive: true });
  window.addEventListener('resize', requestViewportUpdate, { passive: true });

  setActive(cards[0]);
  requestViewportUpdate();
});

// Contact page interactive wizard
const contactWizard = document.querySelector('[data-contact-wizard]');
if (contactWizard) {
  const wizardSteps = [...contactWizard.querySelectorAll('[data-wizard-step]')];
  const progressBar = contactWizard.querySelector('.contact-wizard-progress span');
  const countLabel = contactWizard.querySelector('.contact-wizard-count');
  const backButton = contactWizard.querySelector('[data-wizard-back]');
  const nextButton = contactWizard.querySelector('[data-wizard-next]');
  let wizardIndex = Number.parseInt(contactWizard.dataset.initialStep || '0', 10);

  if (!Number.isFinite(wizardIndex)) wizardIndex = 0;
  wizardIndex = Math.max(0, Math.min(wizardSteps.length - 1, wizardIndex));

  const syncChoiceChip = (input) => {
    const chip = input.closest('.contact-choice-chip');
    if (chip) chip.classList.toggle('selected', input.checked);
  };

  contactWizard.querySelectorAll('.contact-choice-chip input').forEach((input) => {
    syncChoiceChip(input);
    input.addEventListener('change', () => syncChoiceChip(input));
  });

  const validateWizardStep = (index) => {
    const step = wizardSteps[index];
    if (!step) return true;

    const fields = [...step.querySelectorAll('input, select, textarea')]
      .filter((field) => !field.disabled && field.type !== 'hidden');
    const invalidField = fields.find((field) => !field.checkValidity());

    if (!invalidField) return true;
    invalidField.reportValidity();
    invalidField.focus({ preventScroll: true });
    invalidField.scrollIntoView({ behavior: document.body.classList.contains('no-motion') ? 'auto' : 'smooth', block: 'center' });
    return false;
  };

  const renderContactWizard = (shouldFocus = false) => {
    wizardSteps.forEach((step, index) => {
      const active = index === wizardIndex;
      step.hidden = !active;
      step.setAttribute('aria-hidden', active ? 'false' : 'true');
    });

    if (progressBar) progressBar.style.width = `${((wizardIndex + 1) / wizardSteps.length) * 100}%`;
    if (countLabel) countLabel.textContent = `${wizardIndex + 1} of ${wizardSteps.length}`;
    if (backButton) backButton.disabled = wizardIndex === 0;

    const finalStep = wizardIndex === wizardSteps.length - 1;
    if (nextButton) {
      const continueLabel = nextButton.dataset.continueLabel || 'Continue';
      const submitLabel = nextButton.dataset.submitLabel || 'Send message';
      nextButton.type = finalStep ? 'submit' : 'button';
      nextButton.innerHTML = `${finalStep ? submitLabel : continueLabel} <span aria-hidden="true">→</span>`;
    }

    if (shouldFocus) {
      const heading = wizardSteps[wizardIndex]?.querySelector('h3');
      if (heading) {
        heading.setAttribute('tabindex', '-1');
        heading.focus({ preventScroll: true });
      }
      if (window.innerWidth <= 680) {
        contactWizard.scrollIntoView({ behavior: document.body.classList.contains('no-motion') ? 'auto' : 'smooth', block: 'start' });
      }
    }
  };

  if (nextButton) {
    nextButton.addEventListener('click', (event) => {
      if (!validateWizardStep(wizardIndex)) {
        event.preventDefault();
        return;
      }

      const finalStep = wizardIndex === wizardSteps.length - 1;
      if (finalStep) return;

      event.preventDefault();
      wizardIndex = Math.min(wizardSteps.length - 1, wizardIndex + 1);
      renderContactWizard(true);
    });
  }

  if (backButton) {
    backButton.addEventListener('click', () => {
      wizardIndex = Math.max(0, wizardIndex - 1);
      renderContactWizard(true);
    });
  }

  contactWizard.addEventListener('submit', (event) => {
    if (!validateWizardStep(wizardIndex)) {
      event.preventDefault();
      return;
    }

    if (nextButton) {
      nextButton.disabled = true;
      nextButton.textContent = 'Sending…';
    }
  });

  renderContactWizard(false);
}

// Work order request modal
const workOrderModal = document.querySelector('[data-work-order-modal]');
if (workOrderModal) {
  const dialog = workOrderModal.querySelector('.work-order-dialog');
  const form = workOrderModal.querySelector('[data-work-order-form]');
  const workKeyInput = workOrderModal.querySelector('[data-work-order-key]');
  const workTitleLabel = workOrderModal.querySelector('[data-work-order-title]');
  const closeButtons = workOrderModal.querySelectorAll('[data-work-order-close]');
  const orderTriggers = document.querySelectorAll('[data-order-work]');
  let previousFocus = null;

  const focusableSelector = 'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

  const openWorkOrder = (workKey, workTitle, trigger = null, force = false) => {
    if (!workKey && !force) return;

    previousFocus = trigger || document.activeElement;
    workKeyInput.value = workKey;
    workTitleLabel.textContent = workTitle || 'Selected work';
    workOrderModal.hidden = false;
    document.body.classList.add('work-order-modal-open');

    window.requestAnimationFrame(() => {
      workOrderModal.classList.add('is-open');
      const firstInvalid = form?.querySelector(':invalid');
      const firstField = form?.querySelector('input:not([type="hidden"]):not([tabindex="-1"]), select, textarea');
      (firstInvalid || firstField || dialog)?.focus({ preventScroll: true });
    });
  };

  const closeWorkOrder = () => {
    workOrderModal.classList.remove('is-open');
    document.body.classList.remove('work-order-modal-open');
    workOrderModal.hidden = true;

    if (previousFocus instanceof HTMLElement) {
      previousFocus.focus({ preventScroll: true });
    }
  };

  orderTriggers.forEach((trigger) => {
    trigger.addEventListener('click', () => {
      openWorkOrder(trigger.dataset.workKey || '', trigger.dataset.workTitle || '', trigger);
    });
  });

  closeButtons.forEach((button) => button.addEventListener('click', closeWorkOrder));

  workOrderModal.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      event.preventDefault();
      closeWorkOrder();
      return;
    }

    if (event.key !== 'Tab' || !dialog) return;

    const focusable = [...dialog.querySelectorAll(focusableSelector)]
      .filter((element) => !element.hasAttribute('hidden') && element.getClientRects().length > 0);

    if (focusable.length === 0) return;

    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  });

  form?.addEventListener('submit', (event) => {
    if (!workKeyInput.value) {
      event.preventDefault();
      closeWorkOrder();
      document.getElementById('workGrid')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });

  if (workOrderModal.dataset.openOnLoad === 'true') {
    openWorkOrder(
      workOrderModal.dataset.initialWorkKey || '',
      workOrderModal.dataset.initialWorkTitle || 'Choose a work item',
      null,
      true
    );
  }
}
