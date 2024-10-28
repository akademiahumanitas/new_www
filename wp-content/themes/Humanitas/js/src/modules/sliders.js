const sliders = () => {
  const allSliders = document.querySelectorAll('.block-hero-slider__slider');
  const allCardsSliders = document.querySelectorAll('.card-slider');
  const logosSliders = document.querySelectorAll('.block-logo-slider__logos');
  const secondaryMenu = document.querySelector('.secondary-menu__list');
  const controlButtons = document.querySelectorAll('.block-hero-slider__controls-button');

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
    slidesToShow: 3,
    slidesToScroll: 1,
    ...sharedSettings,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 2,
        },
      },
      {
        breakpoint: 671,
        settings: {
          slidesToShow: 1,
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

  const secondaryMenuSettings = {
    autoplay: false,
    dots: false,
    fade: false,
    speed: 450,
    arrows: true,
    infinite: false,
    slidesToScroll: 1,
    slidesToShow: 1,
    centerMode: false,
    variableWidth: true,
    ...sharedSettings,
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

  if (controlButtons.length > 0) {
    // play pause autoplay on click
    controlButtons.forEach((button) => {
      button.addEventListener('click', (e) => {
        e.preventDefault();
        const slider = button.closest('.block-hero-slider').querySelector('.block-hero-slider__slider');
        if (slider) {
          if (slider.classList.contains('slick-initialized')) {
            if (button.classList.contains('autoplay')) {
              jQuery('.block-hero-slider__slider').slick('slickSetOption', 'autoplay', false).slick('slickPause');
              button.classList.remove('autoplay');
            } else {
              jQuery('.block-hero-slider__slider').slick('slickSetOption', 'autoplay', true).slick('slickPlay');
              button.classList.add('autoplay');
            }
          }
        }
      });
    });
  }

  if (secondaryMenu) {
    jQuery(secondaryMenu).not('.slick-initialized').slick(secondaryMenuSettings);

    let childrenWidth = 0;
    jQuery('.secondary-menu__list .slick-track').children().each(function () {
      childrenWidth += jQuery(this).width() + 56;
    });
    const outerContainerWidth = jQuery('.secondary-menu__list').width();

    if (childrenWidth < outerContainerWidth) {
      const nextArrow = jQuery('.secondary-menu__list .slick-next');
      jQuery('.secondary-menu__list').addClass('with-scroll');
      if (!(nextArrow.hasClass('slick-disabled'))) {
        nextArrow.addClass('slick-disabled');
        nextArrow.attr('aria-disabled', 'true');
      }
    }
  }
};

export default sliders;
