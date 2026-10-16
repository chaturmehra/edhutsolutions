document.addEventListener('DOMContentLoaded', () => {
  const navToggle = document.querySelector('[data-nav-toggle]');
  const nav = document.querySelector('[data-nav]');
  const setNav = (open) => {
    if (!nav || !navToggle) {
      return;
    }
    nav.classList.toggle('is-open', open);
    document.body.classList.toggle('nav-open', open);
    navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  };

  const slides = [...document.querySelectorAll('[data-slide]')];
  const slider = document.querySelector('[data-slider]');
  let slideIndex = 0;

  const showSlide = (index) => {
    if (!slides.length) {
      return;
    }
    slideIndex = (index + slides.length) % slides.length;
    slides.forEach((slide, current) => {
      slide.classList.toggle('is-active', current === slideIndex);
      slide.hidden = current !== slideIndex;
    });
  };

  if (slider && slides.length) {
    showSlide(0);
    slider.querySelector('[data-slide-next]')?.addEventListener('click', () => showSlide(slideIndex + 1));
    slider.querySelector('[data-slide-prev]')?.addEventListener('click', () => showSlide(slideIndex - 1));
  }

  document.querySelectorAll('[data-shot-track]').forEach((shotTrack) => {
    const shotDots = shotTrack.nextElementSibling;
    if (!shotDots?.matches('[data-shot-dots]')) {
      return;
    }
    const label = shotTrack.dataset.shotLabel || 'Show image';
    const shots = [...shotTrack.children];
    const centerOf = (shot) => shot.offsetLeft + shot.offsetWidth / 2;
    const dots = shots.map((shot, index) => {
      const dot = document.createElement('button');
      dot.type = 'button';
      dot.className = 'shot-dot';
      dot.setAttribute('aria-label', `${label} ${index + 1}`);
      dot.addEventListener('click', () => {
        shotTrack.scrollTo({ left: centerOf(shot) - shotTrack.clientWidth / 2, behavior: 'smooth' });
      });
      shotDots.appendChild(dot);
      return dot;
    });
    let marking = false;
    const markShot = () => {
      marking = false;
      const middle = shotTrack.scrollLeft + shotTrack.clientWidth / 2;
      let current = 0;
      shots.forEach((shot, index) => {
        if (Math.abs(centerOf(shot) - middle) < Math.abs(centerOf(shots[current]) - middle)) {
          current = index;
        }
      });
      dots.forEach((dot, index) => {
        dot.classList.toggle('is-active', index === current);
        dot.setAttribute('aria-current', index === current ? 'true' : 'false');
      });
    };
    shotTrack.addEventListener('scroll', () => {
      if (!marking) {
        marking = true;
        requestAnimationFrame(markShot);
      }
    }, { passive: true });
    markShot();
  });

  const storyDialog = document.querySelector('[data-story-dialog]');
  if (storyDialog && typeof storyDialog.showModal === 'function') {
    const storyFrame = storyDialog.querySelector('[data-story-frame]');
    document.querySelectorAll('[data-story-youtube]').forEach((card) => {
      card.addEventListener('click', (event) => {
        event.preventDefault();
        const frame = document.createElement('iframe');
        frame.src = `https://www.youtube-nocookie.com/embed/${encodeURIComponent(card.dataset.storyYoutube)}?autoplay=1&rel=0`;
        frame.title = card.getAttribute('aria-label') || 'Video story';
        frame.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
        frame.allowFullscreen = true;
        storyFrame.replaceChildren(frame);
        storyDialog.showModal();
      });
    });
    storyDialog.addEventListener('close', () => storyFrame.replaceChildren());
    storyDialog.querySelector('[data-story-close]')?.addEventListener('click', () => storyDialog.close());
    storyDialog.addEventListener('click', (event) => {
      if (event.target === storyDialog) {
        storyDialog.close();
      }
    });
  }

  const notice = document.querySelector('[data-notice]');
  if (notice) {
    const noticeKey = 'edhut-notice-until';
    const until = Number(window.localStorage.getItem(noticeKey) || 0);
    const setNotice = (open) => {
      notice.classList.toggle('is-open', open);
      notice.setAttribute('aria-hidden', open ? 'false' : 'true');
      document.body.classList.toggle('notice-open', open);
    };
    if (!until || Date.now() >= until) {
      setNotice(true);
    }
    notice.querySelector('[data-notice-close]')?.addEventListener('click', () => {
      setNotice(false);
      window.localStorage.setItem(noticeKey, String(Date.now() + 24 * 60 * 60 * 1000));
    });
  }

  const dialog = document.querySelector('[data-lightbox]');
  const lightboxImage = dialog?.querySelector('[data-lightbox-image]');
  const lightboxCount = dialog?.querySelector('[data-lightbox-count]');
  const lightboxItems = [...document.querySelectorAll('[data-gallery-open]')];
  let lightboxIndex = 0;

  const showLightbox = (index) => {
    if (!dialog || !lightboxImage || !lightboxItems.length) {
      return;
    }
    lightboxIndex = (index + lightboxItems.length) % lightboxItems.length;
    const item = lightboxItems[lightboxIndex];
    lightboxImage.src = item.getAttribute('data-full') || '';
    lightboxImage.alt = item.getAttribute('data-alt') || '';
    if (lightboxCount) {
      lightboxCount.textContent = `${lightboxIndex + 1} / ${lightboxItems.length}`;
    }
    dialog.querySelector('[data-lightbox-prev]')?.toggleAttribute('hidden', lightboxItems.length < 2);
    dialog.querySelector('[data-lightbox-next]')?.toggleAttribute('hidden', lightboxItems.length < 2);
    if (!dialog.open) {
      dialog.showModal();
    }
  };

  lightboxItems.forEach((button, index) => {
    button.addEventListener('click', () => showLightbox(index));
  });
  dialog?.querySelector('[data-lightbox-close]')?.addEventListener('click', () => dialog.close());
  dialog?.querySelector('[data-lightbox-prev]')?.addEventListener('click', () => showLightbox(lightboxIndex - 1));
  dialog?.querySelector('[data-lightbox-next]')?.addEventListener('click', () => showLightbox(lightboxIndex + 1));
  dialog?.addEventListener('click', (event) => {
    if (event.target === dialog || event.target.classList.contains('lightbox-stage')) {
      dialog.close();
    }
  });
  dialog?.addEventListener('keydown', (event) => {
    if (event.key === 'ArrowRight') {
      event.preventDefault();
      showLightbox(lightboxIndex + 1);
    }
    if (event.key === 'ArrowLeft') {
      event.preventDefault();
      showLightbox(lightboxIndex - 1);
    }
  });

  const quotes = [...document.querySelectorAll('[data-quote]')];
  let quotePage = 0;
  const pageSize = 3;
  const showQuotes = (page) => {
    if (!quotes.length) {
      return;
    }
    const pages = Math.ceil(quotes.length / pageSize);
    quotePage = (page + pages) % pages;
    quotes.forEach((card, index) => {
      const visible = index >= quotePage * pageSize && index < (quotePage + 1) * pageSize;
      card.hidden = !visible;
    });
  };
  if (quotes.length) {
    showQuotes(0);
    document.querySelector('[data-quote-next]')?.addEventListener('click', () => showQuotes(quotePage + 1));
    document.querySelector('[data-quote-prev]')?.addEventListener('click', () => showQuotes(quotePage - 1));
  }

  const voiceSlider = document.querySelector('[data-voice-slider]');
  if (voiceSlider) {
    const voices = [...voiceSlider.querySelectorAll('[data-voice]')];
    const dots = voiceSlider.querySelector('[data-voice-dots]');
    const minis = voiceSlider.querySelector('[data-voice-minis]');
    let voiceIndex = 0;

    const showVoice = (next) => {
      if (!voices.length) {
        return;
      }
      voiceIndex = (next + voices.length) % voices.length;
      const voice = voices[voiceIndex];
      voiceSlider.querySelector('[data-voice-quote]').textContent = voice.textContent.trim();
      voiceSlider.querySelector('[data-voice-name]').textContent = voice.dataset.name;
      voiceSlider.querySelector('[data-voice-initials]').textContent = voice.dataset.initials;
      voiceSlider.querySelector('[data-voice-chip]').textContent = voice.dataset.chip;
      voiceSlider.querySelector('[data-voice-tag]').textContent = voice.dataset.headline;
      [...dots.children].forEach((dot, index) => dot.classList.toggle('is-active', index === voiceIndex));
      const steps = Array.from({ length: Math.min(3, voices.length - 1) }, (_, offset) => offset + 1);
      minis.hidden = !steps.length;
      minis.replaceChildren(...steps.map((step) => {
        const item = voices[(voiceIndex + step) % voices.length];
        const card = document.createElement('button');
        card.type = 'button';
        card.className = 'home-card voice-mini';
        card.innerHTML = `<span class="voice-mini-top"><span class="voice-mini-mark" aria-hidden="true"></span><span class="voice-chip"></span></span><strong></strong><p></p>`;
        card.querySelector('.voice-mini-mark').textContent = item.dataset.initials;
        card.querySelector('.voice-chip').textContent = item.dataset.chip;
        card.querySelector('strong').textContent = item.dataset.name;
        card.querySelector('p').textContent = item.textContent.trim().slice(0, 140);
        card.addEventListener('click', () => showVoice(voices.indexOf(item)));
        return card;
      }));
    };

    voices.forEach((_, index) => {
      const dot = document.createElement('button');
      dot.type = 'button';
      dot.className = 'voice-dot';
      dot.setAttribute('aria-label', `Review ${index + 1}`);
      dot.addEventListener('click', () => showVoice(index));
      dots.appendChild(dot);
    });
    showVoice(0);
    voiceSlider.querySelector('[data-voice-next]')?.addEventListener('click', () => showVoice(voiceIndex + 1));
    voiceSlider.querySelector('[data-voice-prev]')?.addEventListener('click', () => showVoice(voiceIndex - 1));
  }

  const mobileAccordion = (selector, query) => {
    const cards = [...document.querySelectorAll(selector)];
    if (!cards.length) {
      return;
    }
    const wide = window.matchMedia(query);
    const sync = () => cards.forEach((card, index) => {
      card.open = wide.matches || index === 0;
    });
    cards.forEach((card) => {
      card.querySelector('summary').addEventListener('click', (event) => {
        if (wide.matches) {
          event.preventDefault();
        }
      });
      card.addEventListener('toggle', () => {
        if (card.open && !wide.matches) {
          cards.forEach((other) => {
            if (other !== card) {
              other.open = false;
            }
          });
        }
      });
    });
    wide.addEventListener('change', sync);
    sync();
  };
  mobileAccordion('[data-overview-card]', '(min-width: 760px)');
  mobileAccordion('[data-agenda-module]', '(min-width: 800px)');

  const subnav = document.querySelector('[data-subnav]');
  if (subnav) {
    const subnavList = subnav.querySelector('ul');
    const subnavPairs = [...subnavList.querySelectorAll('a[href^="#"]')]
      .map((link) => [link, document.getElementById(link.hash.slice(1))])
      .filter(([, target]) => target);
    let subnavCurrent = null;
    let subnavTicking = false;
    const markSubnav = () => {
      subnavTicking = false;
      const line = subnav.getBoundingClientRect().bottom + 24;
      const match = subnavPairs.find(([, target]) => {
        const box = target.getBoundingClientRect();
        return box.top <= line && box.bottom > line;
      });
      const current = match ? match[0] : null;
      if (current === subnavCurrent) {
        return;
      }
      subnavCurrent = current;
      subnavPairs.forEach(([link]) => {
        if (link === current) {
          link.setAttribute('aria-current', 'true');
        } else {
          link.removeAttribute('aria-current');
        }
      });
      if (current && subnavList.scrollWidth > subnavList.clientWidth) {
        const listBox = subnavList.getBoundingClientRect();
        const linkBox = current.getBoundingClientRect();
        subnavList.scrollTo({
          left: subnavList.scrollLeft + linkBox.left - listBox.left - (subnavList.clientWidth - linkBox.width) / 2,
          behavior: 'smooth',
        });
      }
    };
    window.addEventListener('scroll', () => {
      if (!subnavTicking) {
        subnavTicking = true;
        requestAnimationFrame(markSubnav);
      }
    }, { passive: true });
    window.addEventListener('resize', markSubnav);
    markSubnav();
  }

  document.querySelectorAll('[data-tabs]').forEach((tabs) => {
    const buttons = [...tabs.querySelectorAll('[role="tab"]')];
    const usesHash = tabs.dataset.tabs === 'hash';
    const select = (button, focus) => {
      buttons.forEach((other) => {
        const active = other === button;
        other.setAttribute('aria-selected', active ? 'true' : 'false');
        other.tabIndex = active ? 0 : -1;
        const panel = document.getElementById(other.getAttribute('aria-controls'));
        if (panel) {
          panel.hidden = !active;
        }
      });
      button.scrollIntoView({ block: 'nearest', inline: 'nearest' });
      if (focus) {
        button.focus();
      }
      if (usesHash) {
        history.replaceState(null, '', '#' + button.getAttribute('aria-controls'));
      }
    };
    if (usesHash && location.hash) {
      const linked = buttons.find((button) => '#' + button.getAttribute('aria-controls') === location.hash);
      if (linked) {
        select(linked, false);
      }
    }
    buttons.forEach((button, index) => {
      button.addEventListener('click', () => select(button, false));
      button.addEventListener('keydown', (event) => {
        const moves = { ArrowRight: 1, ArrowLeft: -1, Home: -index, End: buttons.length - 1 - index };
        if (!(event.key in moves)) {
          return;
        }
        event.preventDefault();
        select(buttons[(index + moves[event.key] + buttons.length) % buttons.length], true);
      });
    });
  });

  const desktopNav = window.matchMedia('(min-width: 860px)');
  const menuTimers = new WeakMap();
  document.querySelectorAll('.menu').forEach((menu) => {
    menu.addEventListener('mouseenter', () => {
      if (!desktopNav.matches) {
        return;
      }
      clearTimeout(menuTimers.get(menu));
      document.querySelectorAll('.menu').forEach((other) => {
        if (other !== menu) {
          clearTimeout(menuTimers.get(other));
          other.open = false;
        }
      });
      menu.open = true;
    });
    menu.addEventListener('mouseleave', () => {
      if (!desktopNav.matches) {
        return;
      }
      menuTimers.set(menu, setTimeout(() => {
        menu.open = false;
      }, 280));
    });
  });

  document.querySelectorAll('[data-count]').forEach((node) => {
    const raw = String(node.getAttribute('data-count') || '').replace(/,/g, '');
    const match = raw.match(/^(\d+)(.*)$/);
    if (!match) {
      return;
    }
    const target = Number(match[1]);
    const suffix = match[2] || '';
    const paint = (value) => {
      node.textContent = value.toLocaleString('en-US') + suffix;
    };
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      paint(target);
      return;
    }
    const observer = new IntersectionObserver((entries) => {
      if (!entries.some((entry) => entry.isIntersecting)) {
        return;
      }
      observer.disconnect();
      const start = performance.now();
      const tick = (now) => {
        const progress = Math.min(1, (now - start) / 1100);
        paint(Math.round(target * (1 - Math.pow(1 - progress, 3))));
        if (progress < 1) {
          requestAnimationFrame(tick);
        }
      };
      requestAnimationFrame(tick);
    }, { threshold: 0.45 });
    observer.observe(node);
  });

  document.querySelectorAll('[data-plan-tabs]').forEach((root) => {
    const buttons = [...root.querySelectorAll('[data-plan]')];
    const panels = [...root.querySelectorAll('[data-plan-panel]')];
    buttons.forEach((button) => {
      button.addEventListener('click', () => {
        const id = button.getAttribute('data-plan');
        buttons.forEach((item) => {
          const selected = item === button;
          item.classList.toggle('is-selected', selected);
          item.setAttribute('aria-selected', selected ? 'true' : 'false');
        });
        panels.forEach((panel) => {
          panel.hidden = panel.getAttribute('data-plan-panel') !== id;
        });
      });
    });
    const wanted = new URLSearchParams(window.location.search).get('plan');
    if (wanted) {
      buttons.find((button) => button.dataset.planLabel?.toLowerCase() === wanted.toLowerCase())?.click();
    }
  });

  const cartDrawer = document.querySelector('[data-cart-drawer]');
  const cartOpeners = document.querySelectorAll('[data-cart-open]');
  const setCartOpen = (open) => {
    if (!cartDrawer) {
      return;
    }
    cartDrawer.classList.toggle('is-open', open);
    cartDrawer.setAttribute('aria-hidden', open ? 'false' : 'true');
    document.body.classList.toggle('cart-open', open);
    cartOpeners.forEach((button) => button.setAttribute('aria-expanded', open ? 'true' : 'false'));
    if (open) {
      setNav(false);
      cartDrawer.querySelector('.cart-close')?.focus();
    }
  };
  navToggle?.addEventListener('click', () => {
    const open = !nav.classList.contains('is-open');
    setNav(open);
    if (open) {
      setCartOpen(false);
    }
  });
  document.querySelectorAll('[data-nav-close]').forEach((button) => {
    button.addEventListener('click', () => setNav(false));
  });
  cartOpeners.forEach((button) => button.addEventListener('click', () => setCartOpen(true)));
  cartDrawer?.querySelectorAll('[data-cart-close]').forEach((button) => {
    button.addEventListener('click', () => setCartOpen(false));
  });
  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') {
      return;
    }
    if (cartDrawer?.classList.contains('is-open')) {
      setCartOpen(false);
    } else if (nav?.classList.contains('is-open')) {
      setNav(false);
    }
  });

  document.querySelectorAll('[data-toast]').forEach((toast) => {
    const hide = () => toast.remove();
    toast.addEventListener('click', hide);
    window.setTimeout(hide, toast.classList.contains('is-bad') ? 9000 : 6000);
  });

  document.querySelectorAll('[data-digits]').forEach((input) => {
    const keepDigits = () => {
      const digits = input.value.replace(/\D/g, '').slice(0, input.maxLength > 0 ? input.maxLength : undefined);
      if (digits !== input.value) input.value = digits;
    };
    input.addEventListener('beforeinput', (event) => {
      if (event.inputType.startsWith('insert') && event.data && /\D/.test(event.data) && event.inputType !== 'insertFromPaste') {
        event.preventDefault();
      }
    });
    input.addEventListener('input', keepDigits);
    keepDigits();
  });

  document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
      const input = button.parentElement?.querySelector('[data-password]');
      if (!input) {
        return;
      }
      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      button.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
  });

  const cohortModal = document.querySelector('[data-cohort-modal]');
  if (cohortModal) {
    const cohortCards = [...cohortModal.querySelectorAll('[data-cohort-card]')];
    let cohortOpener = null;
    const pickRadio = (name, value) => {
      const radio = value && cohortModal.querySelector(`input[name="${name}"][value="${value}"]`);
      if (radio) {
        radio.checked = true;
      }
    };
    const markCohort = () => {
      cohortCards.forEach((card) => {
        card.classList.toggle('is-selected', Boolean(card.querySelector('input[type="radio"]')?.checked));
      });
      const tier = cohortModal.querySelector('input[name="package"]:checked');
      cohortModal.querySelectorAll('[data-cohort-price]').forEach((node) => {
        node.textContent = tier?.dataset.tierPrice || '';
      });
    };
    const closeCohort = () => {
      cohortModal.hidden = true;
      document.body.classList.remove('cohort-open');
      cohortOpener?.focus();
    };
    document.querySelectorAll('[data-cohort-open]').forEach((button) => {
      button.addEventListener('click', (event) => {
        event.preventDefault();
        cohortOpener = button;
        pickRadio('package', button.dataset.packageId);
        pickRadio('schedule', button.dataset.scheduleId);
        cohortModal.hidden = false;
        document.body.classList.add('cohort-open');
        markCohort();
        const focusTarget = cohortModal.querySelector('input[name="schedule"]:checked') || cohortModal.querySelector('.cohort-x');
        focusTarget?.focus();
      });
    });
    cohortModal.querySelectorAll('[data-cohort-close]').forEach((button) => {
      button.addEventListener('click', closeCohort);
    });
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && !cohortModal.hidden) {
        closeCohort();
      }
    });
    cohortModal.addEventListener('change', markCohort);
    cohortModal.querySelectorAll('[data-cohort-dates]').forEach((toggle) => {
      toggle.addEventListener('click', () => {
        const days = document.getElementById(toggle.getAttribute('aria-controls'));
        const open = toggle.getAttribute('aria-expanded') !== 'true';
        toggle.setAttribute('aria-expanded', String(open));
        toggle.querySelector('[data-cohort-dates-label]').textContent = open ? 'Hide dates' : 'View dates';
        if (days) {
          days.hidden = !open;
        }
      });
    });
  }

  let homeModal = null;
  let homeOpener = null;
  const closeHomeModal = () => {
    if (!homeModal) {
      return;
    }
    homeModal.hidden = true;
    homeModal = null;
    document.body.classList.remove('svc-modal-open');
    homeOpener?.focus();
  };
  document.querySelectorAll('[data-home-modal-open]').forEach((button) => {
    const modal = document.getElementById(button.dataset.homeModalOpen);
    if (!modal) {
      return;
    }
    button.addEventListener('click', () => {
      homeOpener = button;
      homeModal = modal;
      modal.hidden = false;
      document.body.classList.add('svc-modal-open');
      (modal.querySelector('[role="dialog"] input:checked') || modal.querySelector('[role="dialog"] [data-home-modal-close]'))?.focus({ preventScroll: true });
    });
  });
  document.querySelectorAll('[data-home-modal] [data-home-modal-close]').forEach((button) => {
    button.addEventListener('click', closeHomeModal);
  });
  document.addEventListener('keydown', (event) => {
    if (!homeModal) {
      return;
    }
    if (event.key === 'Escape') {
      closeHomeModal();
      return;
    }
    if (event.key === 'Tab') {
      const focusable = [...homeModal.querySelectorAll('[role="dialog"] a[href], [role="dialog"] button')];
      const first = focusable[0];
      const last = focusable[focusable.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    }
  });
});
