const blockOffer = () => {
  const allOfferCards = document.querySelectorAll('button.button-offer-card');
  allOfferCards.forEach((offerCard) => {
    offerCard.addEventListener('click', (e) => {
      const offerId = e.currentTarget.dataset.id;
      const sidebarWithId = document.querySelector(`.block-offer-links__sidebar-single#${offerId}`);
      console.log(e.currentTarget.dataset, offerId, sidebarWithId);
      const allSidebarSingle = document.querySelectorAll('.block-offer-links__sidebar-single');
      allSidebarSingle.forEach((sidebarSingle) => {
        sidebarSingle.classList.remove('block-offer-links__sidebar-single--active');
      });
      sidebarWithId.classList.add('block-offer-links__sidebar-single--active');

      if (window.innerWidth < 599) {
        const sidebarWithIdTop = sidebarWithId.getBoundingClientRect().top;
        const sidebarWithIdTopWithOffset = sidebarWithIdTop + window.scrollY - 100;
        window.scrollTo({
          top: sidebarWithIdTopWithOffset,
          behavior: 'smooth',
        });
      }
    });
  });
};

export default blockOffer;
