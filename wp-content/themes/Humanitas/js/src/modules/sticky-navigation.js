const stickyNavigation = () => {
  const header = document.querySelector('.site-header');
  const headerHeight = header.offsetHeight;
  document.documentElement.style.setProperty('--header-height', `${headerHeight}px`);

  window.addEventListener('scroll', () => {
    const { scrollY } = window;
    if (scrollY > headerHeight) {
      header.classList.add('sticky');
    } else {
      header.classList.remove('sticky');
    }
  });
};

export default stickyNavigation;
