import PropTypes from 'prop-types';
import React, { useCallback } from 'react';
import { useDropzone } from 'react-dropzone';

import createUploader from '../../utils/createUploader';
import useCollection from '../../hooks/useCollection';
import Storage from './Storage';
import Preview from './Preview/index';

const getAcceptForType = type => (['audio', 'video', 'image'].includes(type) ? `${type}/*` : null);

const DropzoneLabel = ({ multiple }) => (
  <span className="zfe-files-ajax-dropzone-label">
    <span>Для загрузки перетащите {multiple ? 'файлы' : 'файл'} в эту область.</span>
  </span>
);

DropzoneLabel.propTypes = {
  multiple: PropTypes.bool.isRequired,
};

const Element = ({
  accept,
  disabled,
  files,
  modelName,
  multiple,
  name,
  previewRender,
  schemaCode,
  type,
  uploadUrl,
  maxUploadFileSize,
  ...options,
}) => {
  const [items, { addItem, updateItem, removeItem }] = useCollection(files);

  const {
    getRootProps,
    getInputProps,
    isDragActive,
    open,
  } = useDropzone({
    onDrop: useCallback((acceptedFiles) => {
      acceptedFiles.forEach((file) => {
        const item = addItem({ loading: true });

        const reader = new FileReader();
        reader.onload = () => { updateItem(item.key, { previewLocal: reader.result }); };
        reader.readAsDataURL(file);

        const uploader = createUploader()
          .setUrl(uploadUrl)
          .setMaxFileSize(maxUploadFileSize)
          .setFile(file)
          .setParams({ modelName, schemaCode })
          .onProgress(({ loaded, total }) => updateItem(item.key, { uploadProgress: loaded / total * 100 }))
          .onComplete(data => updateItem(item.key, { loading: false, ...data }))
          .onError(console.log)
          .start();

        updateItem(item.key, {
          abortUpload: () => uploader.abort(),
          continueUpload: () => uploader.continue(),
        });
      });
    }, []),
    noClick: true,
    multiple,
    disabled,
    accept: accept || getAcceptForType(type),
  });

  const openUploadWindow = (e) => {
    open(e);

    if (!multiple) {
      items.map(item => item.deleted && removeItem(item.key));
    }
  };

  return (
    <div {...getRootProps()} className="zfe-files-ajax-dropzone">
      <input {...getInputProps()} />

      {isDragActive && <DropzoneLabel multiple={multiple} />}

      {(multiple || !items.filter(item => !item.deleted).length) && (
        <button
          className="btn btn-default"
          type="button"
          disabled={disabled}
          onClick={openUploadWindow}
        >
          Загрузить
        </button>
      )}

      <Preview
        previewRender={previewRender}
        type={type}
        items={items}
        onDelete={key => updateItem(key, { deleted: true })}
        onUndelete={key => updateItem(key, { deleted: null })}
        onCancelUpload={key => removeItem(key)}
        setData={(key, data) => updateItem(key, { data })}
        {...options}
      />

      <Storage items={items} name={name} />
    </div>
  );
};

Element.propTypes = {
  accept: PropTypes.string,
  disabled: PropTypes.bool,
  files: PropTypes.arrayOf(PropTypes.object),
  maxUploadFileSize: PropTypes.number,
  modelName: PropTypes.string.isRequired,
  multiple: PropTypes.bool,
  name: PropTypes.string.isRequired,
  previewRender: PropTypes.element,
  schemaCode: PropTypes.string.isRequired,
  type: PropTypes.string,
  uploadUrl: PropTypes.string.isRequired,
};

Element.defaultProps = {
  accept: null,
  disabled: false,
  files: [],
  maxUploadFileSize: 1024 ** 2,
  multiple: false,
  previewRender: null,
  type: null,
};

export default Element;
