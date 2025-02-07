import { nanoid } from 'nanoid';

import { createFormData } from './createFormData';
import { ChunksUploaderProps, Uploader } from './UploaderTypes';

/**
 * @see https://medium.com/@pilovm/multithreaded-file-uploading-with-javascript-dafabce34ccd
 * @todo Добавить ограничение на общее ограничение потоков для всех экземпляров
 */

export class ChunksUploader extends Uploader {
  private onProgress;
  private chunkSize;
  private maxThreads;
  private uploadId = nanoid();
  private chunksCount;
  private chunksQueue;
  private connections: Record<string, XMLHttpRequest> = {};
  private uploadedSize = 0;
  private progressCache: Record<string, number> = {};
  private aborted = false;
  private maxErrors = 10;
  private countError = 0;

  constructor(props: ChunksUploaderProps) {
    super(props);

    this.chunkSize = props.chunkSize ?? 1024 * 1024;
    this.maxThreads = props.maxThreads ?? 1;
    this.onProgress = props.onProgress ?? (() => {});
    this.chunksCount = Math.ceil(this.file.size / this.chunkSize);
    this.chunksQueue = new Array(this.chunksCount)
      .fill(0)
      .map((_, index) => index)
      .reverse();
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

    if (this.chunksQueue.length < 1) {
      // Ожидающих отправки чанков не найдено
      return;
    }

    const chunkNum = this.chunksQueue.pop()!;
    const sentSize = chunkNum * this.chunkSize;
    const chunk = this.file.slice(sentSize, sentSize + this.chunkSize);

    this.sendChunk(chunk, chunkNum)
      .then((data) => {
        // Максимум 10 попыток для одного чанка
        // Как вернем поддержку многопоточной загрузки потребуется доработка для привязки числа ошибок к соединениям
        this.countError = 0;

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

  async sendChunk(chunk: Blob, chunkNum: number) {
    const { status, data } = await this.upload(chunk, chunkNum);
    if (status !== 0 && status !== '0') {
      throw new Error('Не удалось загрузить чанк.');
    }
    return data;
  }

  handleProgress(
    chunkNum: number,
    event: ProgressEvent<XMLHttpRequestEventTarget>,
  ) {
    if (['progress', 'error', 'abort'].includes(event.type)) {
      this.progressCache[chunkNum] = event.loaded;
    }

    if (event.type === 'loadend') {
      this.uploadedSize += this.progressCache[chunkNum] || 0;
      delete this.progressCache[chunkNum];
    }

    const inProgress = Object.keys(this.progressCache).reduce(
      (memo, id) => memo + this.progressCache[id],
      0,
    );
    const sendedLength = Math.min(
      this.uploadedSize + inProgress,
      this.file.size,
    );

    this.onProgress({
      loaded: sendedLength ?? 0,
      total: this.file.size,
    });
  }

  upload(chunk: Blob, chunkNum: number) {
    return new Promise<{ status: number | string; data: { file?: {} } }>(
      (resolve, reject) => {
        // eslint-disable-next-line no-multi-assign
        const xhr = (this.connections[chunkNum] = new XMLHttpRequest());

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

        xhr.send(
          createFormData({
            ...this.params,
            file: chunk,
            fileName: this.file.name,
            fileSize: this.file.size,
            chunksCount: this.chunksCount,
            chunkNum,
            uid: this.uploadId,
          }),
        );
      },
    );
  }
}
