const archiveFilters = () => {
  // get form archive filters
  const form = document.querySelector('.archive-page__form');

  if (!form) return;

  const checkboxes = form?.querySelectorAll('.filter-select__checkbox');
  const checkedCheckboxes = form?.querySelectorAll('.filter-select__checkbox:checked');
  const filterContent = document.querySelector('.archive-page__content-posts');
  const filterReset = document.querySelector('.archive-page__filters-reset');
  const filterNumber = document.querySelector('.archive-page__filters-number');

  filterNumber.innerHTML = checkedCheckboxes.length || '';

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
    filterNumber.innerHTML = cboxes.length || '';

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
};

export default archiveFilters;
