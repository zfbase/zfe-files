import PropTypes from 'prop-types';
import nanoid from 'nanoid';

import createFormData from './createFormData';

/**
 * @see https://medium.com/@pilovm/multithreaded-file-uploading-with-javascript-dafabce34ccd
 * @todo Добавить ограничение на общее ограничение потоков для всех экземпляров
 */

class ChunksUploader {
  constructor(props) {
    this.url = props.url;
    this.chunkSize = props.chunkSize || 1024 * 1024;
    this.maxThreads = props.maxThreads || 2;
    this.file = props.file;
    this.params = props.params || {};
    this.onStart = props.onStart || (() => {});
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
    this.onStart();
    this.sendNext();
  }

  abort() {
    Object.keys(this.connections).forEach((chunkNum) => {
      this.connections[chunkNum].abort();
    });

    this.aborted = true;
  }

  continue() {
    this.aborted = false;
    this.sendNext();
  }

  sendNext() {
    const countConnections = Object.keys(this.connections).length;

    if (countConnections >= this.maxThreads) {
      // Уже запущено максимальное число потоков
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
          this.abort();
          this.onComplete(data.file);
        } else if (!this.aborted) {
          this.sendNext();
        }
      })
      .catch((error) => {
        this.chunksQueue.push(chunkNum);

        // eslint-disable-next-line no-console
        console.log(error);

        this.countError += 1;
        if (this.countError < this.maxErrors) {
          // Если ошибок меньше допустимого числа, перезапустить отправку
          if (!this.aborted) {
            this.sendNext();
          }
        } else if (countConnections === 0) {
          // Если ошибок больше допустимого количества и открытых соединений нет – останавливаемся и плачем
          this.abort();
          this.onError(error);
        }
      });
  }

  async sendChunk(chunk, chunkNum) {
    const { status, data } = await this.upload(chunk, chunkNum);
    if (status !== 0 && status !== '0') {
      throw new Error('Не удалось загрузить чанк.');
    }
    return data;
  }

  handleProgress(chunkNum, event) {
    if (['progress', 'error', 'abort'].includes(event.type)) {
      this.progressCache[chunkNum] = event.loaded;
    }

    if (event.type === 'loadend') {
      this.uploadedSize += this.progressCache[chunkNum] || 0;
      delete this.progressCache[chunkNum];
    }

    const inProgress = Object.keys(this.progressCache).reduce((memo, id) => memo + this.progressCache[id], 0);
    const sendedLength = Math.min(this.uploadedSize + inProgress, this.file.size);

    this.onProgress({
      loaded: sendedLength || 0,
      total: this.file.size,
    });
  }

  upload(chunk, chunkNum) {
    return new Promise((resolve, reject) => {
      // eslint-disable-next-line no-multi-assign
      const xhr = this.connections[chunkNum] = new XMLHttpRequest();

      const progressListener = this.handleProgress.bind(this, chunkNum);
      xhr.upload.addEventListener('progress', progressListener);
      xhr.addEventListener('error', progressListener);
      xhr.addEventListener('abort', progressListener);
      xhr.addEventListener('loadend', progressListener);

      xhr.open('post', this.url);

      xhr.onreadystatechange = () => {
        if (xhr.readyState === 4 && xhr.status === 200) {
          resolve(JSON.parse(xhr.responseText));
          delete this.connections[chunkNum];
        }
      };

      xhr.onerror = (error) => {
        reject(error);
        delete this.connections[chunkNum];
      };

      xhr.onabort = () => {
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
    });
  }
}

ChunksUploader.propTypes = {
  url: PropTypes.string.isRequired,
  chunkSize: PropTypes.number,
  maxThreads: PropTypes.number,
  file: PropTypes.instanceOf(File).isRequired,
  params: PropTypes.object, // eslint-disable-line react/forbid-prop-types
  onStart: PropTypes.func,
  onProgress: PropTypes.func,
  onComplete: PropTypes.func,
  onError: PropTypes.func,
};

ChunksUploader.defaultProps = {
  chunkSize: 1024 * 1024,
  maxThreads: 2,
  params: {},
  onStart: () => {},
  onProgress: () => {},
  onComplete: () => {},
  onError: () => {},
};

export default ChunksUploader;
