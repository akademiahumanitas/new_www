const sliders = () => {
  const allSliders = document.querySelectorAll('.block-hero-slider__slider');
  const allCardsSliders = document.querySelectorAll('.card-slider');
  const logosSliders = document.querySelectorAll('.block-logo-slider__logos');

  const sharedSettings = {
    prevArrow: `<button type="button" class="slick-prev"><span class="screen-reader-text">Previous</span>
    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
    <path d="M17.5606 16.0009L10.9609 9.40124L12.8466 7.51562L21.3318 16.0009L12.8466 24.4861L10.9609 22.6005L17.5606 16.0009Z" fill="var(--color-slick-arrow)"/>
  </svg>
    </button>`,
    nextArrow: `<button type="button" class="slick-next"><span class="screen-reader-text">Next</span>
    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
    <path d="M17.5606 16.0009L10.9609 9.40124L12.8466 7.51562L21.3318 16.0009L12.8466 24.4861L10.9609 22.6005L17.5606 16.0009Z" fill="var(--color-slick-arrow)"/>
  </svg>
    </button >`,
  };

  // slider settings
  const settings = {
    autoplay: true,
    autoplaySpeed: 5000,
    dots: true,
    fade: false,
    pauseOnHover: true,
    pauseOnFocus: true,
    pauseOnDotsHover: false,
    speed: 450,
    arrows: true,
    infinite: false,
    slidesToShow: 1,
    ...sharedSettings,
  };

  const settingsCards = {
    autoplay: false,
    autoplaySpeed: 5000,
    dots: false,
    fade: false,
    speed: 450,
    arrows: true,
    infinite: false,
    slidesToShow: 4,
    slidesToScroll: 1,
    ...sharedSettings,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 2,
        },
      },
    ],
  };

  const settingsLogos = {
    autoplay: true,
    autoplaySpeed: 5000,
    dots: false,
    fade: false,
    speed: 450,
    arrows: false,
    infinite: true,
    slidesToScroll: 1,
    // centerMode: true,
    variableWidth: true,
    slidesToShow: 1,
  };

  // init slider
  allSliders.forEach((slider) => {
    jQuery(slider).not('.slick-initialized').slick(settings);
  });

  allCardsSliders.forEach((slider) => {
    jQuery(slider).not('.slick-initialized').slick(settingsCards);
  });

  logosSliders.forEach((slider) => {
    jQuery(slider).not('.slick-initialized').slick(settingsLogos);
  });
};

export default sliders;
