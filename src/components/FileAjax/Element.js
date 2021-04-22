import PropTypes from 'prop-types';
import React from 'react';
import { useDropzone } from 'react-dropzone';

import createUploader from '../../utils/createUploader';
import pageUnload from '../../utils/pageUnload';
import useCollection from '../../hooks/useCollection';
import Storage from './Storage';
import Preview from './Preview/index';
import validImageMinSize from '../../validators/images/MinSize';

const getAcceptForType = type => (['audio', 'video', 'image'].includes(type) ? `${type}/*` : null);

// Расчет области для кадрирования
const getImageBox = ({
  proxyHeight,
  proxyWidth,
  imageHeight,
  imageWidth,
}) => {
  const xk = imageWidth / proxyWidth;
  const yk = imageHeight / proxyHeight;
  const width = xk <= yk ? imageWidth : Math.round(proxyWidth * yk);
  const height = yk <= xk ? imageHeight : Math.round(proxyHeight * xk);
  return {
    x: Math.floor((imageWidth - width) / 2),
    y: Math.floor((imageHeight - height) / 2),
    width,
    height,
  };
};

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
  uploadBtnLabel,
  maxChunkSize,
  maxFileSize,
  modelName,
  multiple,
  name,
  onLoaded,
  previewRender,
  schemaCode,
  itemId,
  type,
  uploadUrl,
  form,
  ...options
}) => {
  const [items, { addItem, updateItem, removeItem, getItem }] = useCollection(files);

  // Надо использовать валидаторы, указанные в элементе формы
  const valid = (file, settings, success, fail) => {
    if (type === 'image') {
      validImageMinSize(file, settings, success, fail);
    } else {
      success();
    }
  };

  React.useEffect(() => {
    onLoaded();
  }, []);

  const {
    getRootProps,
    getInputProps,
    isDragActive,
    open,
  } = useDropzone({
    onDrop: React.useCallback((acceptedFiles) => {
      acceptedFiles.forEach((file) => {
        if (!multiple) {
          if (items.filter(i => !i.deleted).length) {
            if (!window.confirm('Заменить прикрепленный файл новым?')) {
              return;
            }
          }
          items.forEach(({ key }) => removeItem(key));
        }

        const item = addItem({ loading: true });

        if (type === 'image') {
          const reader = new FileReader();
          reader.onload = () => {
            const {
              height: proxyHeight = null,
              width: proxyWidth = null,
            } = options;

            const image = new Image();
            image.onload = () => {
              const {
                width: imageWidth,
                height: imageHeight,
              } = image;

              const data = getImageBox({
                proxyHeight,
                proxyWidth,
                imageHeight,
                imageWidth,
              });

              const canvas = document.createElement('canvas');
              canvas.width = proxyWidth * 2;
              canvas.height = proxyHeight * 2;
              const context = canvas.getContext('2d');
              context.drawImage(image, data.x, data.y, data.width, data.height, 0, 0, canvas.width, canvas.height);
              context.save();

              updateItem(item.key, {
                data,
                previewLocal: canvas.toDataURL('image/jpg'),
              });
            };
            image.src = reader.result;

            updateItem(item.key, {
              previewLocal: reader.result,
            });
          };
          reader.readAsDataURL(file);
        }

        valid(file, options,
          () => {
            const uploader = createUploader()
              .setUrl(uploadUrl)
              .setMaxChunkSize(Number(maxChunkSize))
              .setFile(file)
              .setParams({ modelName, schemaCode, itemId })
              .onStart(() => pageUnload.disable(form))
              .onProgress(({ loaded, total }) => updateItem(item.key, { uploadProgress: loaded / total * 100 }))
              .onComplete((data) => {
                updateItem(item.key, { loading: false, ...data });
                pageUnload.enable(form);
              })
              // eslint-disable-next-line no-console
              .onError(console.error)
              .start();

            updateItem(item.key, {
              abortUpload: () => uploader.abort(),
              continueUpload: () => uploader.continue(),
            });
          },
          (message) => {
            alert(message);
            removeItem(item.key);
          });
      });
    }, [items]),
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

  const cancelUpload = (key) => {
    getItem(key).abortUpload();
    removeItem(key);
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
          {uploadBtnLabel}
        </button>
      )}

      <Preview
        previewRender={previewRender}
        type={type}
        items={items}
        onDelete={key => updateItem(key, { deleted: true })}
        onUndelete={key => updateItem(key, { deleted: null })}
        onCancelUpload={cancelUpload}
        setData={(key, data) => updateItem(key, { data })}
        {...options}
      />

      <Storage {...{ items, name }} />
    </div>
  );
};

Element.propTypes = {
  accept: PropTypes.string,
  disabled: PropTypes.bool,
  files: PropTypes.arrayOf(PropTypes.object),
  uploadBtnLabel: PropTypes.string,
  maxChunkSize: PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
  maxFileSize: PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
  modelName: PropTypes.string.isRequired,
  multiple: PropTypes.bool,
  name: PropTypes.string.isRequired,
  onLoaded: PropTypes.func,
  previewRender: PropTypes.element,
  schemaCode: PropTypes.string.isRequired,
  itemId: PropTypes.number,
  type: PropTypes.string,
  uploadUrl: PropTypes.string.isRequired,
  form: PropTypes.any,
};

Element.defaultProps = {
  accept: null,
  disabled: false,
  files: [],
  uploadBtnLabel: 'Загрузить',
  maxChunkSize: 1024 ** 2,  // 1 MB
  maxFileSize: 0,  // Unlimited
  multiple: false,
  onLoaded: () => {},
  previewRender: null,
  itemId: null,
  type: null,
  form: null,
};

export default Element;
