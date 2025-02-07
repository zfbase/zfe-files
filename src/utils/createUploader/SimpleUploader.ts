import { createFormData } from './createFormData';
import { SimpleUploaderProps, Uploader } from './UploaderTypes';

export class SimpleUploader extends Uploader {
  private formData;
  private controller;

  constructor(props: SimpleUploaderProps) {
    super(props);

    this.formData = createFormData(this.params);
    this.formData.append('file', this.file);
    this.formData.append('fileSize', this.file.size.toString());

    this.controller = new AbortController();
  }

  start() {
    const body = this.formData;
    const { signal } = this.controller;
    this.onStart();
    fetch(this.url, { method: 'POST', body, signal })
      .then((response) => response.json())
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
