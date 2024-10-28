const secondaryNavigation = () => {
  const automaticMenu = document.querySelector('.secondary-menu--automatic');
  const siteHeader = document.querySelector('.site-header');

  if (!automaticMenu) return;
  document.documentElement.style.setProperty('--secondary-nav-height', `${automaticMenu.offsetHeight}px`);

  // on click on autoamtic menu item with # in the href, scroll to the target
  automaticMenu?.addEventListener('click', (e) => {
    e.preventDefault();
    const target = e.target.getAttribute('href');
    const targetElement = document.querySelector(target);
    targetElement.scrollIntoView({
      behavior: 'smooth',
      block: 'start',
    });
  });

  // when user scrolls and is on section with the ID of the link, add class active
  window.addEventListener('scroll', () => {
    const links = automaticMenu.querySelectorAll('.secondary-menu--automatic a');
    const header = document.querySelector('.site-header');
    const headerHeight = header.offsetHeight;
    document.documentElement.style.setProperty('--header-height', `${headerHeight}px`);

    links.forEach((link, index) => {
      const section = document.querySelector(link.hash);
      const sectionsMargin = parseInt(window.getComputedStyle(section).marginTop, 10);
      const sectionfromTop = section.getBoundingClientRect().y
        - automaticMenu.offsetHeight
        - siteHeader.offsetHeight
        - 40
        - sectionsMargin;

      if (
        sectionfromTop <= 0
            && sectionfromTop + section.offsetHeight > 0
      ) {
        links.forEach((link) => link.blur());
        link.classList.add('active');
        // if (window.innerWidth < 500) {
        jQuery('.secondary-menu__list').slick('slickGoTo', index);
        // }
      } else {
        link.classList.remove('active');
      }
    });
  });
};

export default secondaryNavigation;
