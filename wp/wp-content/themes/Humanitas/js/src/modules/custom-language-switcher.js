const customLanguageSwitcher = () => {
  const languageSwitchers = document.querySelectorAll('.custom-language-switcher');

  languageSwitchers.forEach((languageSwitcher) => {
    const trigger = languageSwitcher.querySelector('.custom-language-switcher__active');
    trigger.addEventListener('click', () => {
      languageSwitcher.classList.toggle('is-open');
    });

    document.addEventListener('click', (event) => {
      if (!languageSwitcher.contains(event.target)) {
        languageSwitcher.classList.remove('is-open');
      }
    });
  });
};

export default customLanguageSwitcher;
