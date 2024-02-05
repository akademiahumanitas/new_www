const offerSearch = () => {
  const search = () => {
    const filterButtons = document.querySelectorAll('.block-offer-search__filters-button');
    const submitButton = document.querySelector('.block-offer-search__submit-button');
    const activeButtons = document.querySelectorAll('.block-offer-search__filters-button.active');
    const activeButtonsArray = Array.from(activeButtons);
    const groupedButtons = activeButtonsArray.reduce((acc, b) => {
      const { taxonomy } = b.dataset;
      const { term } = b.dataset;
      if (acc[taxonomy]) {
        acc[taxonomy].push(term);
      } else {
        acc[taxonomy] = [term];
      }

      return acc;
    }, {});

    const groupedButtonsWithNames = activeButtonsArray.reduce((acc, b) => {
      const { taxonomy } = b.dataset;
      const { termName } = b.dataset;

      if (acc[taxonomy]) {
        acc[taxonomy].push(termName);
      } else {
        acc[taxonomy] = [termName];
      }

      return acc;
    }, {});

    const taxonomyString = Object.keys(groupedButtons).map((taxonomy) => `${taxonomy}=${groupedButtons[taxonomy].join(',')}`).join('&');
    const buttonString = Object.keys(groupedButtonsWithNames).map((taxonomy) => `${taxonomy}=${groupedButtonsWithNames[taxonomy].join(',')}`).join('&');
    console.log(buttonString);

    const url = `/wp-json/wp/v2/oferta/?${taxonomyString}`;

    submitButton.setAttribute('href', `${submitButton.dataset?.offerHref}?${buttonString}`);

    fetch(url)
      .then((response) => response.json())
      .then((data) => {
        const allTerms = data.reduce((acc, post) => {
          const taxonomies = ['offer_language', 'offer_category', 'offer_location', 'offer_format', 'offer_type'];
          const terms = taxonomies.reduce((a, taxonomy) => {
            const t = post[taxonomy];
            return [...a, ...t];
          }, []);

          return [...acc, ...terms];
        }, []);

        if (allTerms.length) {
          filterButtons.forEach((b) => {
            b.setAttribute('disabled', '');
          });
          allTerms.forEach((term) => {
            const matchingButton = document.querySelector(`.block-offer-search__filters-button[data-term="${term}"]`);
            matchingButton?.removeAttribute('disabled');
          });
        } else {
          filterButtons.forEach((b) => {
            b.removeAttribute('disabled');
          });
        }
      })
      .catch((error) => console.error(error));
  };

  const filterButtons = document.querySelectorAll('.block-offer-search__filters-button');

  if (filterButtons.length) {
    search();
    filterButtons.forEach((button) => {
      button.addEventListener('click', (e) => {
        e.preventDefault();

        button.classList.toggle('active');
        search();
      });
    });
  }
};

export default offerSearch;
