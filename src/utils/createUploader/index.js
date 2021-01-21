import SimpleUploader from './SimpleUploader';
import ChunksUploader from './ChunksUploader';

export default function (props = {}) {
  let { url, file } = props;
  let maxFileSize = props.maxFileSize || 1024 * 1024;
  let params = props.params || {};
  let onProgress = props.onProgress || (() => {});
  let onComplete = props.onComplete || (() => {});
  let onError = props.onError || (() => {});
  let uploader;

  return {

    //
    // Методы установки параметров загрузки
    //

    /**
     * Установить адрес для загрузки
     * @param string value
     */
    setUrl(value) {
      if (uploader) {
        throw new Error('Указать адрес для загрузки можно только до начала загрузки.');
      }

      url = value;
      return this;
    },

    /**
     * Установить максимальный размер загружаемого файла
     * @param string value
     */
    setMaxFileSize(value) {
      if (uploader) {
        throw new Error('Указать максимальный размер файла для одного запроса можно только до начала загрузки.');
      }

      maxFileSize = value;
      return this;
    },

    /**
     * Установить загружаемый файл
     * @param string value
     */
    setFile(value) {
      if (uploader) {
        throw new Error('Указать файл можно только до начала загрузки.');
      }

      file = value;
      return this;
    },

    /**
     * Установить дополнительные параметры загрузки
     * @param string value
     */
    setParams(value) {
      if (uploader) {
        throw new Error('Указать дополнительные параметры запроса загрузки можно только до начала загрузки.');
      }

      params = value;
      return this;
    },


    //
    // Обработчики событий
    //

    /**
     * Установить обработчик на событие начала загрузки файла
     * @param func callback
     */
    onStart(callback) {
      if (uploader) {
        throw new Error('Указать обработчик прогресса начала загрузки можно только до начала загрузки.');
      }

      onProgress = callback;
      return this;
    },

    /**
     * Установить обработчик на событие шага процесса загрузки (перед каждым шагом)
     * @param func callback
     */
    onProgress(callback) {
      if (uploader) {
        throw new Error('Указать обработчик прогресса загрузки можно только до начала загрузки.');
      }

      onProgress = callback;
      return this;
    },

    /**
     * Установить обработчик на событие успешного завершения загрузки
     * @param func callback
     */
    onComplete(callback) {
      if (uploader) {
        throw new Error('Указать обработчик успешного завершения загрузки можно только до начала загрузки.');
      }

      onComplete = callback;
      return this;
    },

    /**
     * Установить событие на событие завершение загрузки с ошибкой
     * @param func callback
     */
    onError(callback) {
      if (uploader) {
        throw new Error('Указать обработчик ошибок можно только до начала загрузки.');
      }

      onError = callback;
      return this;
    },


    //
    // Управляющие методы
    //

    /**
     * Начать загрузку
     */
    start() {
      if (!url) {
        throw new Error('До начала загрузки необходимо указать путь для загрузки.');
      }
      if (!file) {
        throw new Error('До начала загрузки необходимо указать файл.');
      }

      const commonProps = {
        url,
        file,
        params,
        onProgress,
        onComplete,
        onError,
      };

      if (file.size < maxFileSize) {
        uploader = new SimpleUploader(commonProps);
      } else {
        uploader = new ChunksUploader({
          ...commonProps,
          chunkSize: maxFileSize,
        });
      }

      uploader.start();
      return this;
    },

    /**
     * Остановить загрузку
     */
    abort() {
      if (uploader) {
        uploader.abort();
      } else {
        console.warn('Невозможно остановить загрузку до ее начала.');
      }
    },

    /**
     * Продолжить загрузку
     */
    continue() {
      if (uploader) {
        uploader.continue();
      } else {
        console.warn('Невозможно перезапустить загрузку до ее начала.');
      }
    },

  };
}
