const secondaryNavigation = () => {
  const automaticMenu = document.querySelector('.secondary-menu--automatic');

  if (!automaticMenu) return;

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
};

export default secondaryNavigation;
