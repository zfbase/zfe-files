import { ChunksUploader, type ChunksUploaderOptions } from './ChunksUploader';
import { SimpleUploader } from './SimpleUploader';

import { FormDataPayload } from './createFormData';

interface GetUploaderOptions extends ChunksUploaderOptions {
  maxChunkSize?: number;
}

export function createUploader(options: GetUploaderOptions) {
  let { url, file } = options;
  let maxChunkSize = options.maxChunkSize ?? 1024 ** 2;
  let payload = options.payload ?? {};
  let onStart = options.onStart;
  let onProgress = options.onProgress;
  let onComplete = options.onComplete;
  let onError = options.onError;

  let uploader: SimpleUploader | ChunksUploader | undefined;

  return {
    //
    // Методы установки параметров загрузки
    //

    /**
     * Установить адрес для загрузки
     * @param string value
     */
    setUrl(value: string) {
      if (uploader) {
        throw new Error(
          'Указать адрес для загрузки можно только до начала загрузки.',
        );
      }

      url = value;
      return this;
    },

    /**
     * Установить максимальный размер загружаемого файла в одном запросе (файлы, большего размера, загружаются частями)
     * @param string value
     */
    setMaxChunkSize(value: number) {
      if (uploader) {
        throw new Error(
          'Указать максимальный размер файла для одного запроса можно только до начала загрузки.',
        );
      }

      maxChunkSize = value;
      return this;
    },

    /**
     * Установить загружаемый файл
     * @param string value
     */
    setFile(value: File) {
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
    setPayload(value: FormDataPayload) {
      if (uploader) {
        throw new Error(
          'Указать дополнительные параметры запроса загрузки можно только до начала загрузки.',
        );
      }

      payload = Object.keys(value).reduce<FormDataPayload>((acc, key) => {
        if (value[key] !== null && value[key] !== undefined) {
          acc[key] = value[key];
        }
        return acc;
      }, {});
      return this;
    },

    //
    // Обработчики событий
    //

    /**
     * Установить обработчик на событие начала загрузки файла
     * @param func callback
     */
    onStart(callback: () => void) {
      if (uploader) {
        throw new Error(
          'Указать обработчик прогресса начала загрузки можно только до начала загрузки.',
        );
      }

      onStart = callback;
      return this;
    },

    /**
     * Установить обработчик на событие шага процесса загрузки (перед каждым шагом)
     * @param func callback
     */
    onProgress(callback: () => void) {
      if (uploader) {
        throw new Error(
          'Указать обработчик прогресса загрузки можно только до начала загрузки.',
        );
      }

      onProgress = callback;
      return this;
    },

    /**
     * Установить обработчик на событие успешного завершения загрузки
     * @param func callback
     */
    onComplete(callback: () => void) {
      if (uploader) {
        throw new Error(
          'Указать обработчик успешного завершения загрузки можно только до начала загрузки.',
        );
      }

      onComplete = callback;
      return this;
    },

    /**
     * Установить событие на событие завершение загрузки с ошибкой
     * @param func callback
     */
    onError(callback: () => void) {
      if (uploader) {
        throw new Error(
          'Указать обработчик ошибок можно только до начала загрузки.',
        );
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
        throw new Error(
          'До начала загрузки необходимо указать путь для загрузки.',
        );
      }
      if (!file) {
        throw new Error('До начала загрузки необходимо указать файл.');
      }

      const commonProps = {
        url,
        file,
        payload,
        onStart,
        onProgress,
        onComplete,
        onError,
      };

      if (file.size < maxChunkSize) {
        uploader = new SimpleUploader(commonProps);
      } else {
        uploader = new ChunksUploader({
          ...commonProps,
          chunkSize: maxChunkSize,
          maxThreads: options.maxThreads,
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
        // eslint-disable-next-line no-console
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
        // eslint-disable-next-line no-console
        console.warn('Невозможно перезапустить загрузку до ее начала.');
      }
    },
  };
}
