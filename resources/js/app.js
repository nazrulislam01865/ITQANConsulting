const nav = document.getElementById('navlinks');
const menuBtn = document.getElementById('menuBtn');

if (menuBtn && nav) {
  menuBtn.addEventListener('click', () => {
    const open = nav.classList.toggle('open');
    menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
  });

  nav.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
      nav.classList.remove('open');
      menuBtn.setAttribute('aria-expanded', 'false');
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
let currentCatalogPage = 0;
let catalogMuted = true;

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
  const leftIndex = Math.max(0, currentCatalogPage - (currentCatalogPage % 2));
  const rightIndex = Math.min(catalogPages.length - 1, leftIndex + 1);
  leftPage.innerHTML = catalogPageHtml(catalogPages[leftIndex], leftIndex + 1);
  rightPage.innerHTML = catalogPageHtml(catalogPages[rightIndex], rightIndex + 1);
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
