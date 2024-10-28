import addMultipleEventListeners from './navigation/add-multiple-event-listeners';

const megaMenu = () => {
  const siteHeader = document.querySelector('.site-header');
  const firstTierLinks = document.querySelectorAll('.mega-menu__wrapper > .mega-menu__list > .mega-menu__item--has-submenu > .mega-menu__link');
  const secondTierLinks = document.querySelectorAll('.mega-menu .mega-menu__submenu-item-link--has-submenu');
  const megaMenuClose = document.querySelectorAll('.js-close-megamenu');
  const subMenuClose = document.querySelectorAll('.js-close-submenu');
  const hamburger = document.querySelector('.js-hamburger');

  const closeMegaMenu = () => {
    if (window.innerWidth > 767) {
      siteHeader.classList.remove('mega-menu-open');
    }
    firstTierLinks.forEach((link) => {
      link.parentNode.classList.remove('mega-menu__item--open');
    });
    document.querySelectorAll('.mega-menu__submenu').forEach((el) => {
      el.classList.remove('is-open');
    });
    secondTierLinks.forEach((el) => {
      el.classList.remove('is-open');
    });
  };
  const openMegaMenu = (item) => {
    const header = document.querySelector('.site-header');
    const headerHeight = header.offsetHeight;
    document.documentElement.style.setProperty('--header-height', `${headerHeight}px`);

    if (window.innerWidth > 767) {
      siteHeader.classList.add('mega-menu-open');
    }
    item.parentNode.classList.add('mega-menu__item--open');
  };

  firstTierLinks.forEach((link) => {
    addMultipleEventListeners(link, ['click', 'keydown', 'keypress'], (e) => {
      if (e.type === 'click' || e.keyCode === 13 || e.keyCode === 32) {
        e.preventDefault();
        // if this parentnode is open, close megamenu
        if (link.parentNode.classList.contains('mega-menu__item--open')) {
          closeMegaMenu();
        } else {
          closeMegaMenu();
          openMegaMenu(link);
        }
      }
    });
  });

  addMultipleEventListeners(hamburger, ['click', 'keydown', 'keypress'], (e) => {
    if (e.type === 'click' || e.keyCode === 13 || e.keyCode === 32) {
      e.preventDefault();
      // if this parentnode is open, close megamenu
      if (siteHeader.classList.contains('mega-menu-open')) {
        siteHeader.classList.remove('mega-menu-open');
        closeMegaMenu();
      } else {
        siteHeader.classList.add('mega-menu-open');
      }
    }
  });

  secondTierLinks.forEach((link) => {
    addMultipleEventListeners(link, ['click', 'keydown', 'keypress'], (e) => {
      if (e.type === 'click' || e.keyCode === 13 || e.keyCode === 32) {
        e.preventDefault();
        // get link data-for
        const target = link.getAttribute('data-for');
        // get target
        const targetEl = document.getElementById(target);
        document.querySelectorAll('.mega-menu__submenu').forEach((el) => {
          el.classList.remove('is-open');
        });
        secondTierLinks.forEach((el) => {
          el.classList.remove('is-open');
        });
        // open target Element
        targetEl.classList.add('is-open');
        link.classList.add('is-open');
      }
    });
  });

  megaMenuClose.forEach((button) => {
    addMultipleEventListeners(button, ['click', 'keydown', 'keypress'], (e) => {
      if (e.type === 'click' || e.keyCode === 13 || e.keyCode === 32) {
        e.preventDefault();
        closeMegaMenu();
      }
    });
  });
  subMenuClose.forEach((button) => {
    addMultipleEventListeners(button, ['click', 'keydown', 'keypress'], (e) => {
      if (e.type === 'click' || e.keyCode === 13 || e.keyCode === 32) {
        e.preventDefault();
        document.querySelectorAll('.mega-menu__submenu').forEach((el) => {
          el.classList.remove('is-open');
        });
        secondTierLinks.forEach((el) => {
          el.classList.remove('is-open');
        });
      }
    });
  });
};

export default megaMenu;
