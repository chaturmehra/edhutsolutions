(() => {
  const guarded = document.querySelectorAll('[data-protect]');
  if (!guarded.length) return;

  guarded.forEach((box) => {
    ['contextmenu', 'dragstart', 'copy'].forEach((type) => box.addEventListener(type, (event) => event.preventDefault()));
  });

  document.addEventListener('keydown', (event) => {
    const key = event.key.toLowerCase();
    if ((event.ctrlKey || event.metaKey) && (key === 's' || key === 'p')) event.preventDefault();
  });

  const viewer = document.querySelector('[data-pdf-viewer]');
  if (!viewer || !window.pdfjsLib) return;

  const pagesBox = viewer.querySelector('[data-pdf-pages]');
  const status = viewer.querySelector('[data-pdf-status]');
  const scaleLabel = viewer.querySelector('[data-pdf-scale]');
  const mark = pagesBox.dataset.watermark || '';
  const ratio = window.devicePixelRatio || 1;
  let zoom = 1;
  let pdf = null;
  let renderRun = 0;

  window.pdfjsLib.GlobalWorkerOptions.workerSrc = viewer.dataset.worker;

  const stamp = (context, width, height) => {
    if (!mark) return;
    const size = Math.max(12, Math.round(width / 38));
    context.save();
    context.font = `600 ${size}px sans-serif`;
    context.fillStyle = 'rgba(91, 33, 230, 0.12)';
    context.translate(width / 2, height / 2);
    context.rotate(-Math.PI / 6);
    const step = size * 9;
    for (let y = -height; y < height; y += step) {
      for (let x = -width; x < width; x += context.measureText(mark).width + size * 4) {
        context.fillText(mark, x, y);
      }
    }
    context.restore();
  };

  const render = async () => {
    const run = ++renderRun;
    pagesBox.replaceChildren();
    const percent = Math.round(zoom * 100);
    scaleLabel.textContent = `${percent}%`;
    pagesBox.className = pagesBox.className.replace(/\s?is-zoom-\d+/g, '') + (percent === 100 ? '' : ` is-zoom-${percent}`);
    const padding = parseFloat(getComputedStyle(pagesBox).paddingLeft) * 2;
    const available = (pagesBox.clientWidth - padding) || 800;

    for (let number = 1; number <= pdf.numPages; number += 1) {
      if (run !== renderRun) return;
      const page = await pdf.getPage(number);
      const base = page.getViewport({ scale: 1 });
      const viewport = page.getViewport({ scale: (available / base.width) * zoom * ratio });
      const canvas = document.createElement('canvas');
      canvas.className = 'pdf-page';
      canvas.width = Math.floor(viewport.width);
      canvas.height = Math.floor(viewport.height);
      canvas.setAttribute('aria-label', `Page ${number}`);
      pagesBox.appendChild(canvas);
      const context = canvas.getContext('2d');
      await page.render({ canvasContext: context, viewport }).promise;
      stamp(context, canvas.width, canvas.height);
    }
  };

  viewer.querySelectorAll('[data-pdf-zoom]').forEach((button) => {
    button.addEventListener('click', () => {
      const next = Math.round((zoom + Number(button.dataset.pdfZoom) * 0.25) * 100) / 100;
      if (next < 0.5 || next > 2 || !pdf) return;
      zoom = next;
      render();
    });
  });

  window.pdfjsLib.getDocument({
    url: viewer.dataset.src,
    httpHeaders: { 'X-Requested-With': 'XMLHttpRequest' },
    withCredentials: true,
    isEvalSupported: false,
  }).promise.then((doc) => {
    pdf = doc;
    status.textContent = `${doc.numPages} ${doc.numPages === 1 ? 'page' : 'pages'}`;
    return render();
  }).catch(() => {
    status.textContent = 'This PDF could not be loaded. Refresh the page to try again.';
  });
})();
