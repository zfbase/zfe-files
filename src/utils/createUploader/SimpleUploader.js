import PropTypes from 'prop-types';

import createFormData from './createFormData';

class SimpleUploader {
  constructor(props) {
    this.url = props.url;
    this.file = props.file;
    this.params = props.params || {};
    this.onStart = props.onStart || (() => {});
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
    const { signal } = this.controller;
    this.onStart();
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

SimpleUploader.propTypes = {
  url: PropTypes.string.isRequired,
  file: PropTypes.instanceOf(File).isRequired,
  params: PropTypes.object, // eslint-disable-line react/forbid-prop-types
  onStart: PropTypes.func,
  onProgress: PropTypes.func,
  onComplete: PropTypes.func,
  onError: PropTypes.func,
};

SimpleUploader.defaultProps = {
  params: {},
  onStart: () => {},
  onProgress: () => {},
  onComplete: () => {},
  onError: () => {},
};

export default SimpleUploader;
