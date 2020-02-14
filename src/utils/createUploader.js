import nanoid from "nanoid";

const createFormData = (params) => {
  const formData = new FormData();
  Object.entries(params).map(([key, value]) => formData.append(key, value));
  return formData;
};


class SimpleUploader {
  constructor(props) {
    this.url = props.url;
    this.file = props.file;
    this.params = props.params || {};
    this.onProgress = props.onProgress || (() => {});
    this.onComplete = props.onComplete || (() => {});
    this.onError = props.onError || (() => {});

    this.formData = createFormData(this.params);
    this.formData.append('file', this.file);
    this.formData.append('fileSize', this.file.size);

    this.controller = new AbortController();
  }

  start() {
    const body = this.formData;
    const signal = this.controller.signal;
    fetch(this.url, { method: 'POST', body, signal })
      .then(response => response.json())
      .then(({ status, data }) => {
        if ([0, '0'].includes(status) && data.file) {
          this.onComplete(data.file);
        } else {
          throw new Error('Не удалось загрузить файл.');
        }
      })
      .catch(this.onError);
  }

  abort() {
    this.controller.abort();
  }

  continue() {
    this.start();
  }
}


class ChunksUploader {
  constructor(props) {
    this.url = props.url;
    this.chunkSize = props.chunkSize || 1024 * 1024;
    this.maxThreads = props.maxThreads || 2;
    this.file = props.file;
    this.params = props.params || {};
    this.onProgress = props.onProgress || (() => {});
    this.onComplete = props.onComplete || (() => {});
    this.onError = props.onError || (() => {});

    this.uploadId = nanoid();
    this.chunksCount = Math.ceil(this.file.size / this.chunkSize);
    this.chunksQueue = new Array(this.chunksCount).fill().map((_, index) => index).reverse();
    this.connections = {};
    this.uploadedSize = 0;
    this.progressCache = {};
    this.aborted = false;
    this.maxErrors = 10;
    this.countError = 0;
  }

  start() {
    this.sendNext();
  }

  abort(loging = true) {
    this.aborted = true;
  }

  continue() {
    this.aborted = false;
    this.sendNext();
  }

  sendNext() {
    const countConnections = Object.keys(this.connections).length;

    if (countConnections >= this.maxThreads) {
      // Уже запущено макимальное число потоков
      return;
    }

    if (!this.chunksQueue.length) {
      // Ожидающих отправки чанков не найдено
      return;
    }


    const chunkNum = this.chunksQueue.pop();
    const sentSize = chunkNum * this.chunkSize;
    const chunk = this.file.slice(sentSize, sentSize + this.chunkSize);

    this.sendChunk(chunk, chunkNum)
      .then((data) => {
        if (data.file) {
          this.abort(false);
          this.onComplete(data.file);
        } else {
          this.sendNext();
        }
      })
      .catch((error) => {
        this.chunksQueue.push(chunkNum);

        console.log(error);

        this.countError++;
        if (this.countError < this.maxErrors) {
          // Если ошибок меньше допустимого числа, перзапустить отправку
          this.sendNext();
        } else if (countConnections === 0) {
          // Если ошибок больше допустимого колличества и открытых соединений нет – останавливаемся и плачем
          this.abort(false);
          this.onError(error);
        }
      });
  }

  sendChunk(chunk, chunkNum) {
    return new Promise(async (resolve, reject) => {
      try {
        const { status, data } = await this.upload(chunk, chunkNum);
        if (status !== 0 && status !== '0') {
          reject(new Error('Не удалось загрузить чанк.'));
          return;
        }
        resolve(data);
      } catch (error) {
        reject(error);
        return;
      }
    });
  }

  handleProgress(chunkNum, event) {
    if (['progress', 'error', 'abort'].includes(event.type)) {
      this.progressCache[chunkNum] = event.loaded;
    }

    if (event.type === 'loadend') {
      this.uploadedSize += this.progressCache[chunkNum] || 0;
      delete this.progressCache[chunkNum];
    }

    const inProgress = Object.keys(this.progressCache).reduce((memo, id) => memo += this.progressCache[id], 0);
    const sendedLength = Math.min(this.uploadedSize + inProgress, this.file.size);

    this.onProgress({
      loaded: sendedLength,
      total: this.file.size,
    });
  }

  upload(chunk, chunkNum) {
    return new Promise((resolve, reject) => {
      const xhr = this.connections[chunkNum] = new XMLHttpRequest();

      const progressListener = this.handleProgress.bind(this, chunkNum);
      xhr.upload.addEventListener('progress', progressListener);
      xhr.addEventListener('error', progressListener);
      xhr.addEventListener('abort', progressListener);
      xhr.addEventListener('loadend', progressListener);

      xhr.open('post', this.url);

      xhr.onreadystatechange = (event) => {
        if (xhr.readyState === 4 && xhr.status === 200) {
          resolve(JSON.parse(xhr.responseText));
          delete this.connections[chunkNum];
        }
      };

      xhr.onerror = (error) => {
        reject(error);
        delete this.connections[chunkNum];
      };

      xhr.onabort = (error) => {
        console.log(error);
        reject(new Error('Загрузка остановлена пользователем.'));
        delete this.connections[chunkNum];
      };

      xhr.send(createFormData({
        ...this.params,
        file: chunk,
        fileName: this.file.name,
        fileSize: this.file.size,
        chunksCount: this.chunksCount,
        chunkNum,
        uid: this.uploadId,
      }));
    })
  }
}


export default function (props = {}) {
  let url = props.url;
  let maxFileSize = props.maxFileSize || 1024 * 1024;
  let file = props.file;
  let params = props.params || {};
  let onProgress = props.onProgress || (() => {});
  let onComplete = props.onComplete || (() => {});
  let onError = props.onError || (() => {});
  let uploader;

  return {

    //
    // Методы установки параметров загрузки
    //

    setUrl: function (value) {
      if (uploader) {
        throw new Error('Указать адрес для загрузки можно только до начала загрузки.');
      }

      url = value;
      return this;
    },

    setMaxFileSize: function (value) {
      if (uploader) {
        throw new Error('Указать максимальный размер файла для одного запроса можно только до начала загрузки.');
      }

      maxFileSize = value;
      return this;
    },

    setFile: function (value) {
      if (uploader) {
        throw new Error('Указать файл можно только до начала загрузки.');
      }

      file = value;
      return this;
    },

    setParams: function (value) {
      if (uploader) {
        throw new Error('Указать дополнительные параметры запроса загрузки можно только до начала загрузки.');
      }

      params = value;
      return this;
    },


    //
    // Обработчики событий
    //

    onProgress: function (callback) {
      if (uploader) {
        throw new Error('Указать обработчик прогресса загрузки можно только до начала загрузки.');
      }

      onProgress = callback;
      return this;
    },

    onComplete: function (callback) {
      if (uploader) {
        throw new Error('Указать обработчик успешного заверешния загрузки можно только до начала загрузки.');
      }

      onComplete = callback;
      return this;
    },

    onError: function (callback) {
      if (uploader) {
        throw new Error('Указать обработчик ошибок можно только до начала загрузки.');
      }

      onError = callback;
      return this;
    },


    //
    // Управляющие методы
    //

    start: function () {
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

    abort: function () {
      if (uploader) {
        uploader.abort();
      } else {
        console.warn('Невозможно остановить загрузку до ее начала.');
      }
    },

    continue: function () {
      if (uploader) {
        uploader.abort();
      } else {
        console.warn('Невозможно перезапустить загрузку до ее начала.');
      }
    },

  };
}
