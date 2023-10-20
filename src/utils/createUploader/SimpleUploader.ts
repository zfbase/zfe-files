import { ChunksUploaderOptions, UploadResult } from './ChunksUploader';
import createFormData, { FormDataPayload } from './createFormData';

type SimpleUploaderOptions = Omit<
  ChunksUploaderOptions,
  'chunkSize' | 'maxThreads'
>;

class SimpleUploader {
  private abortController;
  private formData;

  constructor(private options: SimpleUploaderOptions) {
    this.formData = createFormData(this.options.payload);
    this.formData.append('file', this.options.file);
    this.formData.append('fileSize', this.options.file.size.toString());

    this.abortController = new AbortController();
  }

  start() {
    const body = this.formData;
    const { signal } = this.abortController;
    this.options.onStart?.();

    fetch(this.options.url, { method: 'POST', body, signal })
      .then((response) => response.json() as Promise<UploadResult>)
      .then(({ status, data }) => {
        if ([0, '0'].includes(status) && data.file) {
          this.options.onComplete?.(data.file);
        } else {
          throw new Error('Не удалось загрузить файл.');
        }
      })
      .catch(this.options.onError);
  }

  abort() {
    this.abortController.abort();
  }

  continue() {
    this.start();
  }
}

export default SimpleUploader;
