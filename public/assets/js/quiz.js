document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('[data-quiz-form]');
  if (!form) {
    return;
  }

  const steps = [...form.querySelectorAll('[data-quiz-step]')];
  const dots = [...document.querySelectorAll('[data-quiz-go]')];
  const prev = form.querySelector('[data-quiz-prev]');
  const next = form.querySelector('[data-quiz-next]');
  const submit = form.querySelector('[data-quiz-submit]');
  const current = document.querySelector('[data-quiz-current]');
  const answeredCount = document.querySelector('[data-quiz-answered]');
  const bar = document.querySelector('[data-quiz-bar]');
  const storageKey = form.dataset.quizKey;
  let index = 0;
  let submitting = false;

  const isAnswered = (step) => [...step.querySelectorAll('input, textarea')].some((field) => {
    if (field.type === 'radio' || field.type === 'checkbox') {
      return field.checked;
    }
    return field.value.trim() !== '';
  });

  const save = () => {
    if (!storageKey) {
      return;
    }
    const data = {};
    form.querySelectorAll('[name^="answers"]').forEach((field) => {
      if ((field.type === 'radio' || field.type === 'checkbox')) {
        if (field.checked) {
          (data[field.name] ||= []).push(field.value);
        }
      } else if (field.value !== '') {
        data[field.name] = field.value;
      }
    });
    try {
      localStorage.setItem(storageKey, JSON.stringify(data));
    } catch (error) {
      // Storage can be full or blocked; answers still post with the form.
    }
  };

  const restore = () => {
    let data = null;
    try {
      data = JSON.parse(localStorage.getItem(storageKey) || 'null');
    } catch (error) {
      data = null;
    }
    if (!data) {
      return;
    }
    form.querySelectorAll('[name^="answers"]').forEach((field) => {
      const value = data[field.name];
      if (value === undefined) {
        return;
      }
      if (field.type === 'radio' || field.type === 'checkbox') {
        field.checked = Array.isArray(value) && value.includes(field.value);
      } else {
        field.value = value;
      }
    });
  };

  const refresh = () => {
    const answered = steps.filter(isAnswered).length;
    steps.forEach((step, i) => {
      step.hidden = i !== index;
      dots[i]?.classList.toggle('is-current', i === index);
      dots[i]?.classList.toggle('is-done', isAnswered(step));
      if (i === index) {
        dots[i]?.setAttribute('aria-current', 'step');
      } else {
        dots[i]?.removeAttribute('aria-current');
      }
    });
    if (current) current.textContent = String(index + 1);
    if (answeredCount) answeredCount.textContent = String(answered);
    if (bar) bar.style.width = `${steps.length ? ((index + 1) / steps.length) * 100 : 0}%`;
    prev.disabled = index === 0;
    next.hidden = index === steps.length - 1;
    submit.hidden = index !== steps.length - 1;
  };

  const go = (target) => {
    index = Math.max(0, Math.min(steps.length - 1, target));
    refresh();
    const focusable = steps[index].querySelector('input, textarea');
    focusable?.focus({ preventScroll: true });
  };

  restore();
  form.classList.add('is-stepped');
  refresh();

  prev.addEventListener('click', () => go(index - 1));
  next.addEventListener('click', () => go(index + 1));
  dots.forEach((dot) => dot.addEventListener('click', () => go(Number(dot.dataset.quizGo))));
  form.addEventListener('change', () => {
    save();
    refresh();
  });
  form.addEventListener('input', save);
  form.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' && event.target.matches('input[type="text"]')) {
      event.preventDefault();
      if (index < steps.length - 1) {
        go(index + 1);
      }
    }
  });

  const finish = () => {
    submitting = true;
    try {
      localStorage.removeItem(storageKey);
    } catch (error) {
      // Nothing to clear.
    }
  };

  form.addEventListener('submit', (event) => {
    if (submitting) {
      return;
    }
    const left = steps.length - steps.filter(isAnswered).length;
    if (left > 0 && !window.confirm(`You have ${left} unanswered ${left === 1 ? 'question' : 'questions'}. Submit anyway?`)) {
      event.preventDefault();
      return;
    }
    finish();
    submit.disabled = true;
  });

  form.querySelectorAll('[data-quiz-reveal]').forEach((button) => {
    const answer = document.getElementById(button.getAttribute('aria-controls'));
    const label = button.querySelector('[data-quiz-reveal-label]');
    const step = button.closest('[data-quiz-step]');
    button.addEventListener('click', () => {
      const open = button.getAttribute('aria-expanded') !== 'true';
      button.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (answer) answer.hidden = !open;
      if (label) label.textContent = open ? 'Hide answer' : 'Show answer';
      step?.classList.toggle('is-revealed', open);
    });
  });

  const timer = document.querySelector('[data-quiz-timer]');
  const clock = document.querySelector('[data-quiz-clock]');
  if (!timer || !clock) {
    return;
  }

  const format = (seconds) => {
    const safe = Math.max(0, Math.floor(seconds));
    const hours = Math.floor(safe / 3600);
    const minutes = Math.floor((safe % 3600) / 60);
    const secs = safe % 60;
    const pad = (value) => String(value).padStart(2, '0');
    return hours > 0 ? `${hours}:${pad(minutes)}:${pad(secs)}` : `${pad(minutes)}:${pad(secs)}`;
  };

  const timed = timer.dataset.remaining !== undefined;
  // For a timed test this is the moment time runs out; otherwise the moment the test began.
  let anchor = timed
    ? Date.now() + Number(timer.dataset.remaining) * 1000
    : Date.now() - Number(timer.dataset.elapsed || 0) * 1000;
  let handle = null;
  let leftAt = null;

  const timeUp = () => {
    if (submitting) {
      return;
    }
    save();
    finish();
    form.querySelector('[data-quiz-timeout]').value = '1';
    const notice = document.querySelector('[data-quiz-timeup]');
    if (notice) notice.hidden = false;
    form.submit();
  };

  const tick = () => {
    if (!timed) {
      clock.textContent = format((Date.now() - anchor) / 1000);
      return;
    }
    const left = (anchor - Date.now()) / 1000;
    clock.textContent = format(Math.ceil(left));
    timer.classList.toggle('is-low', left <= 60);
    if (left <= 0) {
      window.clearInterval(handle);
      timeUp();
    }
  };

  handle = window.setInterval(tick, timed ? 250 : 1000);
  tick();

  const heartbeatUrl = form.dataset.quizHeartbeat;
  const token = form.querySelector('input[name="_token"]')?.value;
  if (!heartbeatUrl || !token) {
    return;
  }

  const payload = (leaving) => {
    const data = new FormData();
    data.append('_token', token);
    if (leaving) data.append('leaving', '1');
    return data;
  };

  const sync = (state) => {
    if (!state || !state.open) {
      return;
    }
    if (timed && typeof state.remaining === 'number') {
      anchor = Date.now() + state.remaining * 1000;
    } else if (!timed && typeof state.elapsed === 'number') {
      anchor = Date.now() - state.elapsed * 1000;
    }
    tick();
  };

  const checkIn = () => {
    if (submitting) {
      return;
    }
    fetch(heartbeatUrl, {
      method: 'POST',
      body: payload(false),
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    })
      .then((response) => (response.ok ? response.json() : null))
      .then(sync)
      .catch(() => {
        // The server still counts this session up to the last check-in.
      });
  };

  window.setInterval(checkIn, 20000);

  window.addEventListener('pagehide', () => {
    if (submitting) {
      return;
    }
    leftAt = Date.now();
    if (navigator.sendBeacon) {
      navigator.sendBeacon(heartbeatUrl, payload(true));
    }
  });

  window.addEventListener('pageshow', (event) => {
    if (!event.persisted || leftAt === null) {
      return;
    }
    // Restored from the back/forward cache: the clock was paused while the learner was away.
    anchor += Date.now() - leftAt;
    leftAt = null;
    tick();
    checkIn();
  });
});
