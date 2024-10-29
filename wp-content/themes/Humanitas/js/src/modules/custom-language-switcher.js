import addMultipleEventListeners from './navigation/add-multiple-event-listeners';

const customLanguageSwitcher = () => {
  const languageSwitchers = document.querySelectorAll('.custom-language-switcher');

  languageSwitchers.forEach((languageSwitcher) => {
    const trigger = languageSwitcher.querySelector('.custom-language-switcher__active');
    addMultipleEventListeners(trigger, ['click', 'keydown', 'keypress'], (e) => {
      if (e.type === 'click' || e.keyCode === 13 || e.keyCode === 32) {
        e.preventDefault();
        languageSwitcher.classList.toggle('is-open');
      }
    });

    document.addEventListener('click', (event) => {
      if (!languageSwitcher.contains(event.target)) {
        languageSwitcher.classList.remove('is-open');
      }
    });
  });
};

export default customLanguageSwitcher;
