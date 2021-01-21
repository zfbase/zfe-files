const beforeunload = (e) => {
  e.preventDefault();
  e.returnValue = true;
};

const submit = (e) => {
  window.alert('Необходимо дождаться завершения загрузки файлов.');
  e.preventDefault();
  e.returnValue = true;
};

export default {

  /**
   * Заблокировать покидание страницы.
   * @param node form
   */
  disable: (form) => {
    window.addEventListener('beforeunload', beforeunload);
    form.addEventListener('submit', submit);
    form.querySelector('[type="submit"]').setAttribute('disabled', 'disabled');
  },

  /**
   * Разблокировать покидание страницы
   * @param node form
   */
  enable: (form) => {
    form.querySelector('[type="submit"]').removeAttribute('disabled');
    form.removeEventListener('submit', submit);
    window.removeEventListener('beforeunload', beforeunload);
  },

};
