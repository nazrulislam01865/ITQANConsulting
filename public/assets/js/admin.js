document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.querySelector('.sidebar');
  const sidebarScrollKey = 'itqan-admin-sidebar-scroll-top';

  if (sidebar) {
    const savedScrollTop = Number.parseInt(localStorage.getItem(sidebarScrollKey) || '0', 10);

    if (!Number.isNaN(savedScrollTop) && savedScrollTop > 0) {
      requestAnimationFrame(() => {
        sidebar.scrollTop = savedScrollTop;
      });
    }

    let scrollTimer = null;
    sidebar.addEventListener('scroll', () => {
      if (scrollTimer) {
        window.clearTimeout(scrollTimer);
      }

      scrollTimer = window.setTimeout(() => {
        localStorage.setItem(sidebarScrollKey, String(sidebar.scrollTop));
      }, 80);
    }, { passive: true });

    sidebar.querySelectorAll('a[href]').forEach((link) => {
      link.addEventListener('click', () => {
        localStorage.setItem(sidebarScrollKey, String(sidebar.scrollTop));
      });
    });
  }

  document.querySelectorAll('[data-nav-group]').forEach((group) => {
    const toggle = group.querySelector('[data-nav-toggle]');
    if (!toggle) return;

    const storageKey = toggle.dataset.storageKey || `admin-nav-${toggle.textContent.trim().toLowerCase().replace(/\s+/g, '-')}`;
    const savedState = localStorage.getItem(storageKey);

    if (toggle.classList.contains('active')) {
      group.classList.add('open');
      toggle.setAttribute('aria-expanded', 'true');
      localStorage.setItem(storageKey, 'open');
    } else if (savedState !== null) {
      group.classList.toggle('open', savedState === 'open');
      toggle.setAttribute('aria-expanded', savedState === 'open' ? 'true' : 'false');
    }

    toggle.addEventListener('click', () => {
      const isOpen = group.classList.toggle('open');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      localStorage.setItem(storageKey, isOpen ? 'open' : 'closed');

      if (sidebar) {
        localStorage.setItem(sidebarScrollKey, String(sidebar.scrollTop));
      }
    });
  });

  // Preserve main page scroll for admin action buttons and form submissions.
  // This prevents Add/Edit/Save/Delete actions from sending the admin back to the top.
  const adminMainScrollKey = 'itqan-admin-main-scroll-y';
  const adminMainScrollFlag = 'itqan-admin-restore-main-scroll';

  if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
  }

  const restoreMainScroll = () => {
    if (sessionStorage.getItem(adminMainScrollFlag) !== '1') {
      return;
    }

    const savedScrollY = Number.parseInt(sessionStorage.getItem(adminMainScrollKey) || '0', 10);

    if (!Number.isNaN(savedScrollY) && savedScrollY > 0) {
      window.scrollTo({ top: savedScrollY, behavior: 'auto' });
      window.setTimeout(() => window.scrollTo({ top: savedScrollY, behavior: 'auto' }), 80);
    }

    sessionStorage.removeItem(adminMainScrollFlag);
  };

  const saveMainScroll = () => {
    sessionStorage.setItem(adminMainScrollKey, String(window.scrollY || window.pageYOffset || 0));
    sessionStorage.setItem(adminMainScrollFlag, '1');
  };

  restoreMainScroll();

  document.querySelectorAll('a[href]:not([target])').forEach((link) => {
    link.addEventListener('click', saveMainScroll);
  });

  document.querySelectorAll('form').forEach((form) => {
    form.addEventListener('submit', saveMainScroll);
  });



  // Admin idle-session timeout: redirect to login automatically after inactivity.
  const timeoutMeta = document.querySelector('meta[name="admin-session-timeout-seconds"]');
  const expiredUrlMeta = document.querySelector('meta[name="admin-session-expired-url"]');
  const timeoutSeconds = Number.parseInt(timeoutMeta?.getAttribute('content') || '0', 10);
  const expiredUrl = expiredUrlMeta?.getAttribute('content') || '';

  if (timeoutSeconds > 0 && expiredUrl) {
    let timeoutHandle = null;
    const resetSessionTimeout = () => {
      if (timeoutHandle) {
        window.clearTimeout(timeoutHandle);
      }

      timeoutHandle = window.setTimeout(() => {
        sessionStorage.removeItem(adminMainScrollFlag);
        sessionStorage.removeItem(adminMainScrollKey);
        window.location.replace(expiredUrl);
      }, timeoutSeconds * 1000);
    };

    ['click', 'mousemove', 'keydown', 'scroll', 'touchstart'].forEach((eventName) => {
      window.addEventListener(eventName, resetSessionTimeout, { passive: true });
    });

    resetSessionTimeout();
  }


  // Mobile admin sidebar drawer.
  const adminMenuToggle = document.querySelector('[data-admin-menu-toggle]');
  const adminMenuOverlay = document.querySelector('[data-admin-sidebar-close]');
  const adminMobileBreakpoint = window.matchMedia('(max-width: 1000px)');

  const setAdminMobileMenu = (isOpen) => {
    document.body.classList.toggle('admin-mobile-menu-open', isOpen);

    if (adminMenuToggle) {
      adminMenuToggle.classList.toggle('is-open', isOpen);
      adminMenuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      adminMenuToggle.setAttribute('aria-label', isOpen ? 'Close admin menu' : 'Open admin menu');
    }
  };

  if (adminMenuToggle) {
    adminMenuToggle.addEventListener('click', () => {
      setAdminMobileMenu(!document.body.classList.contains('admin-mobile-menu-open'));
    });
  }

  if (adminMenuOverlay) {
    adminMenuOverlay.addEventListener('click', () => setAdminMobileMenu(false));
  }

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      setAdminMobileMenu(false);
    }
  });

  if (sidebar) {
    sidebar.querySelectorAll('a[href]').forEach((link) => {
      link.addEventListener('click', () => {
        if (adminMobileBreakpoint.matches) {
          setAdminMobileMenu(false);
        }
      });
    });
  }

  adminMobileBreakpoint.addEventListener?.('change', (event) => {
    if (!event.matches) {
      setAdminMobileMenu(false);
    }
  });

  document.documentElement.classList.remove('admin-nav-hydrating');

});
