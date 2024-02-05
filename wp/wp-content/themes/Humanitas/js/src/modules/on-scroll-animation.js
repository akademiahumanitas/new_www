const onScrollAnimation = () => {
// make an observer that checks if .fade-in elements are in the viewport
// if in viewport add .animate class

  const fadeInElements = document.querySelectorAll('.fade-in');

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        setTimeout(() => {
          entry.target.classList.add('animate');
          observer.unobserve(entry.target);
        }, 500);
      }
    });
  }, {
    rootMargin: '-100px 0px',
  });

  fadeInElements.forEach((element) => {
    observer.observe(element);
  });
};

export default onScrollAnimation;
