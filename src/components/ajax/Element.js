import PropTypes from 'prop-types';
import React, { useCallback } from 'react';
import { useDropzone } from 'react-dropzone';

import useCollection from '../../hooks/useCollection';
import Storage from './Storage';
import Preview from './Preview/index';

const getAcceptForType = type => ['audio', 'video', 'image'].includes(type) ? `${type}/*` : null;

const DropzoneLabel = ({ multiple }) => (
  <span className="zfe-files-ajax-dropzone-label">
    <span>Для загрузки перетащите {multiple ? 'файлы' : 'файл'} в эту область.</span>
  </span>
);

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
}) => {
  const [items, { addItem, updateItem, removeItem }] = useCollection(files);

  const { getRootProps, getInputProps, isDragActive, open } = useDropzone({
    onDrop: useCallback(acceptedFiles => {
      acceptedFiles.forEach((file) => {
        const item = addItem({ loading: true });

        const reader = new FileReader();
        reader.onload = () => { updateItem(item.key, { previewLocal: reader.result }); };
        reader.readAsDataURL(file);

        const formData = new FormData();
        formData.append('file', file);
        formData.append('model_name', modelName);
        formData.append('schema_code', schemaCode)
        fetch(uploadUrl, { method: 'POST', body: formData })
          .then(response => response.json())
          .then(response => {
            if (response.status != '0') {
              throw new Error(response.message);
            }

            updateItem(item.key, { loading: false, ...response.data.file });
          })
          .catch(console.log);
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
      items.map((item) => item.deleted && removeItem(item.key));
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
      />

      <Storage items={items} name={name} />
    </div>
  );
};

Element.propTypes = {
  accept: PropTypes.string,
  disabled: PropTypes.bool,
  files: PropTypes.arrayOf(PropTypes.object),
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
  multiple: false,
  previewRender: null,
  type: null,
};

export default Element;
