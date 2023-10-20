const beforeunload = (e: Event) => {
  e.preventDefault();
  e.returnValue = true;
};

const submit = (e: Event) => {
  // eslint-disable-next-line no-alert
  window.alert('Необходимо дождаться завершения загрузки файлов.');
  e.preventDefault();
  e.returnValue = true;
};

export const pageUnload = {
  /**
   * Заблокировать покидание страницы.
   * @param node|null form
   */
  disable: (form: HTMLFormElement) => {
    window.addEventListener('beforeunload', beforeunload);
    if (form) {
      form.addEventListener('submit', submit);
      const btn = form.querySelector('[type="submit"]');
      if (btn) {
        btn.setAttribute('disabled', 'disabled');
      }
    }
  },

  /**
   * Разблокировать покидание страницы
   * @param node|null form
   */
  enable: (form: HTMLFormElement) => {
    if (form) {
      const btn = form.querySelector('[type="submit"]');
      if (btn) {
        btn.removeAttribute('disabled');
      }
      form.removeEventListener('submit', submit);
    }
    window.removeEventListener('beforeunload', beforeunload);
  },
};
