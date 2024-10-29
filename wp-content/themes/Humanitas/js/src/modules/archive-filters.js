const archiveFilters = () => {
  // get form archive filters
  const form = document.querySelector('.archive-page__form');
  if (!form) return;

  const archiveToggle = document.querySelector('.archive-page__filters-toggle');
  const closeButton = document.querySelector('.archive-page__filters-close');

  const checkboxes = form?.querySelectorAll('.filter-select__checkbox');
  const checkedCheckboxes = form?.querySelectorAll('.filter-select__checkbox:checked');
  const filterContent = document.querySelector('.archive-page__content-posts');
  const filterReset = document.querySelector('.archive-page__filters-reset');
  const filterNumber = document.querySelectorAll('.js-selected-filters');

  const numberValue = checkedCheckboxes.length || '';
  filterNumber.forEach((number) => {
    // eslint-disable-next-line no-param-reassign
    number.textContent = numberValue;
    if (numberValue > 0) {
      number.parentElement.classList.add('active');
    } else {
      number.parentElement.classList.remove('active');
    }
  });

  const toggleSidebar = () => {
    const sidebar = document.querySelector('.archive-page__filters-wrapper');
    if (sidebar.classList.contains('archive-page__filters-wrapper--open')) {
      sidebar.classList.remove('archive-page__filters-wrapper--open');
    } else {
      sidebar.classList.add('archive-page__filters-wrapper--open');
    }
  };
  // on form submit
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const cboxes = form?.querySelectorAll('.filter-select__checkbox:checked');

    const data = {};
    const url = new URL(window.location.href);
    url.search = '';

    formData.forEach((value, name) => {
      data[name] = formData.getAll(name);
    });

    formData.forEach((value, name) => {
      if (name !== 'post_type') {
        if (formData.getAll(name).length > 1) {
          url.searchParams.set(name, formData.getAll(name).join(','));
        } else {
          url.searchParams.set(name, formData.get(name));
        }
      }
    });

    window.history.replaceState({}, '', url);
    filterNumber.forEach((number) => {
      // eslint-disable-next-line no-param-reassign
      number.textContent = cboxes.length || '';
      // add this parent class active
      if (cboxes.length > 0) {
        number.parentElement.classList.add('active');
      } else {
        number.parentElement.classList.remove('active');
      }
    });

    // eslint-disable-next-line no-undef
    fetch(wp.ajaxurl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
      },
      body: new URLSearchParams({
        action: 'archive_filters',
        data: JSON.stringify(data),
      }),
    })
      .then((response) => response.text())
      .then((html) => {
      // replace archive content with response
        filterContent.innerHTML = html;
        const pagination = document.querySelectorAll('a.page-numbers');
        if (pagination) {
          pagination.forEach((page) => {
            // get href
            const href = page.getAttribute('href');
            // get urls param paged
            const url = new URL(href);
            const paged = url.searchParams.get('paged');
            // get current url and add paged to it
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('paged', paged);
            // set new href
            page.setAttribute('href', currentUrl);
          });
        }
        // if window width is less than 1024px close sidebar
        if (window.innerWidth < 991) {
          toggleSidebar();
        }
      });
  });

  // on reset click
  filterReset.addEventListener('click', () => {
    form.reset();
    checkboxes.forEach((checkbox) => {
      const c = checkbox;
      c.checked = false;
      c.removeAttribute('checked');
    });
    form.dispatchEvent(new Event('submit'));
  });

  // on checkbox change submit form
  // checkboxes.forEach((checkbox) => {
  //   checkbox.addEventListener('change', () => {
  //     form.dispatchEvent(new Event('submit'));
  //   });
  // });

  // on toggle click
  archiveToggle.addEventListener('click', () => {
    toggleSidebar();
  });

  // on close click
  closeButton.addEventListener('click', () => {
    toggleSidebar();
  });
};

export default archiveFilters;
