(() => {
  const dialog = document.querySelector('[data-video-dialog]');
  if (!dialog) return;

  const heading = dialog.querySelector('[data-video-heading]');
  const fileBox = dialog.querySelector('[data-video-file]');
  const video = fileBox.querySelector('video');
  const mark = dialog.querySelector('[data-video-mark]');
  const frame = dialog.querySelector('[data-video-frame]');
  let opener = null;

  const stop = () => {
    video.pause();
    video.removeAttribute('src');
    video.load();
    frame.src = 'about:blank';
    fileBox.hidden = true;
    frame.hidden = true;
  };

  document.querySelectorAll('[data-video-open]').forEach((button) => {
    button.addEventListener('click', () => {
      stop();
      opener = button;
      const title = button.dataset.videoTitle || 'Video';
      heading.textContent = title;
      if (button.dataset.videoType === 'frame') {
        frame.title = title;
        frame.src = button.dataset.videoSrc;
        frame.hidden = false;
      } else {
        mark.hidden = !('videoProtected' in button.dataset);
        video.src = button.dataset.videoSrc;
        fileBox.hidden = false;
        video.play().catch(() => {
          // Autoplay can be blocked; the controls stay available.
        });
      }
      dialog.showModal();
    });
  });

  dialog.querySelectorAll('[data-video-close]').forEach((button) => button.addEventListener('click', () => dialog.close()));
  dialog.addEventListener('click', (event) => {
    if (event.target === dialog) dialog.close();
  });
  dialog.addEventListener('close', () => {
    stop();
    opener?.focus();
    opener = null;
  });
})();
