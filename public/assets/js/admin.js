document.addEventListener('DOMContentLoaded', () => {
  const shell = document.querySelector('.admin-shell');
  document.querySelector('[data-side-collapse]')?.addEventListener('click', () => {
    shell?.classList.toggle('is-collapsed');
  });
  document.querySelector('[data-side-open]')?.addEventListener('click', () => {
    shell?.classList.toggle('is-open');
  });
  document.querySelector('[data-side-scrim]')?.addEventListener('click', () => {
    shell?.classList.remove('is-open');
  });
  document.querySelectorAll('.alert').forEach((alert) => {
    window.setTimeout(() => {
      alert.classList.add('is-leaving');
      window.setTimeout(() => alert.remove(), 400);
    }, 30000);
  });

  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  document.querySelectorAll('[data-enquiry-open]').forEach((button) => {
    const dialog = document.getElementById(button.getAttribute('data-enquiry-open'));
    if (!dialog) {
      return;
    }
    button.addEventListener('click', async () => {
      dialog.showModal();
      if (button.dataset.status !== 'new' || !csrf) {
        return;
      }
      const response = await fetch(button.dataset.readUrl, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
      }).catch(() => null);
      if (!response?.ok) {
        return;
      }
      button.dataset.status = 'read';
      const row = button.closest('[data-enquiry-row]');
      [row, dialog].forEach((scope) => {
        scope?.querySelectorAll('[data-enquiry-status]').forEach((tag) => {
          tag.textContent = 'Read';
          tag.classList.remove('is-wait');
        });
      });
      const toggle = row?.querySelector('[data-enquiry-toggle]');
      if (toggle) {
        toggle.textContent = 'Mark new';
      }
    });
  });
  document.querySelectorAll('.enquiry-dialog').forEach((dialog) => {
    dialog.querySelectorAll('[data-dialog-close]').forEach((close) => {
      close.addEventListener('click', () => dialog.close());
    });
    dialog.addEventListener('click', (event) => {
      if (event.target === dialog) {
        dialog.close();
      }
    });
  });

  document.querySelectorAll('[data-auto-submit]').forEach((field) => {
    field.addEventListener('change', () => field.form?.requestSubmit());
  });
  document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      const message = form.getAttribute('data-confirm');
      if (message && !window.confirm(message)) {
        event.preventDefault();
      }
    });
  });

  document.querySelectorAll('[data-source-type]').forEach((select) => {
    const syncSource = () => {
      document.querySelectorAll('[data-source-panel]').forEach((panel) => {
        panel.hidden = panel.getAttribute('data-source-panel') !== select.value;
      });
    };
    select.addEventListener('change', syncSource);
    syncSource();
  });

  const answerType = document.querySelector('[data-answer-type]');
  if (answerType) {
    const choiceHelp = {
      mcq: 'Add at least two options and mark exactly one as correct.',
      multiple: 'Add at least two options and mark every correct option.',
      true_false: 'Mark either True or False as the correct answer.',
      scenario: 'Add at least two options and mark exactly one as correct.',
      fill_blank: 'Add every answer that should be accepted. Capital letters are ignored.',
      short: 'Add every answer that should be accepted. Capital letters are ignored.',
    };
    const accepted = ['fill_blank', 'short'];
    const syncAnswerType = () => {
      const type = answerType.value;
      const options = document.querySelector('[data-answer-options]');
      const scenario = document.querySelector('[data-scenario]');
      const heading = document.querySelector('[data-option-heading]');
      const help = document.querySelector('[data-option-help]');
      const add = document.querySelector('[data-option-add]');
      if (scenario) {
        scenario.hidden = type !== 'scenario';
      }
      if (options) {
        options.hidden = type === 'essay';
      }
      if (heading) {
        heading.textContent = accepted.includes(type) ? 'Accepted answers' : 'Answer options';
      }
      if (help) {
        help.textContent = choiceHelp[type] || '';
      }
      if (add) {
        add.hidden = type === 'essay' || type === 'true_false';
      }
      document.querySelectorAll('[data-option-correct]').forEach((mark) => {
        mark.hidden = accepted.includes(type) || type === 'essay';
      });
      document.querySelectorAll('[data-option-row]').forEach((row, index) => {
        const text = row.querySelector('input[type="text"]');
        const lock = type === 'true_false' && index > 1;
        row.hidden = lock;
        row.querySelectorAll('input').forEach((input) => {
          input.disabled = type === 'essay' || lock;
        });
        if (type === 'true_false' && text && index === 0) {
          text.value = 'True';
        }
        if (type === 'true_false' && text && index === 1) {
          text.value = 'False';
        }
      });
    };
    answerType.addEventListener('change', syncAnswerType);
    syncAnswerType();
  }

  document.querySelector('[data-option-add]')?.addEventListener('click', () => {
    const list = document.querySelector('[data-option-list]');
    const rows = list?.querySelectorAll('[data-option-row]');
    if (!list || !rows?.length || rows.length >= 8) {
      return;
    }
    const index = rows.length;
    const clone = rows[index - 1].cloneNode(true);
    clone.querySelectorAll('input').forEach((input) => {
      input.name = input.name.replace(/options\[\d+\]/, `options[${index}]`);
      if (input.type === 'checkbox') {
        input.checked = false;
      } else {
        input.value = '';
      }
      if (input.type === 'text') {
        input.placeholder = `Option ${index + 1}`;
      }
    });
    list.appendChild(clone);
  });

  const dateList = document.querySelector('[data-date-list]');
  if (dateList) {
    document.querySelector('[data-date-add]')?.addEventListener('click', () => {
      const rows = dateList.querySelectorAll('[data-date-row]');
      if (rows.length >= 60) {
        return;
      }
      const last = rows[rows.length - 1];
      const clone = last.cloneNode(true);
      const input = clone.querySelector('input');
      input.value = '';
      dateList.appendChild(clone);
      input.focus();
    });
    dateList.addEventListener('click', (event) => {
      const remove = event.target.closest('[data-date-remove]');
      if (!remove) {
        return;
      }
      const rows = dateList.querySelectorAll('[data-date-row]');
      if (rows.length > 1) {
        remove.closest('[data-date-row]').remove();
      } else {
        rows[0].querySelector('input').value = '';
      }
    });
  }
});
