const wcagSettings = () => {
  // get all buttons
  const body = document.querySelector('body');
  const fontSize = localStorage.getItem('fontSize');
  const contrast = localStorage.getItem('contrast');
  const fontSizeButtons = document.querySelectorAll(
    '.header-right__wcag-button--big, .header-right__wcag-button--normal, .header-right__wcag-button--bigger',
  );
  const contrastButton = document.querySelector('.header-right__wcag-button--contrast');

  if (fontSize) {
    body.classList.add(`font-size-${fontSize}`);
  }

  if (contrast) {
    body.classList.add('contrast-on');
  }

  fontSizeButtons?.forEach((button) => {
    button.addEventListener('click', () => {
      // check if body has class that starts with font-size-
      // if it does, remove it
      const fontSizeClass = Array.from(body.classList).find((className) => className.startsWith('font-size-'));
      if (fontSizeClass) {
        body.classList.remove(fontSizeClass);
      }

      const newFontSize = button.dataset.fontsize;
      body.classList.add(`font-size-${newFontSize}`);
      localStorage.setItem('fontSize', newFontSize);
    });
  });

  contrastButton?.addEventListener('click', () => {
    // check if body has class contrast-on or if cookie has contrast-on
    // if it does, remove it
    const isContrastOn = body.classList.contains('contrast-on')
      || localStorage.getItem('contrast') === 'contrast-on';

    if (isContrastOn) {
      body.classList.remove('contrast-on');
      localStorage.removeItem('contrast');
      return;
    }

    // if it doesn't, add it
    body.classList.add('contrast-on');
    localStorage.setItem('contrast', 'contrast-on');
  });
};

export default wcagSettings;
