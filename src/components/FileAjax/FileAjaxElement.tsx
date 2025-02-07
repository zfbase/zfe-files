import { useCallback, useState } from 'react';
import { useDropzone } from 'react-dropzone';
import { createUploader } from '../../utils/createUploader';
import { pageUnload } from '../../utils/pageUnload';
import { useCollection } from '../../hooks/useCollection';
import { Storage } from './Storage';
import { FileAjaxPreview } from './Preview/FileAjaxPreview';
import { validImageMinSize } from '../../validators/images/validImageMinSize';

function getAcceptForType(type: string) {
  return ['audio', 'video', 'image'].includes(type) ? `${type}/*` : null;
}

// Расчет области для кадрирования
function getImageBox({
  proxyHeight,
  proxyWidth,
  imageHeight,
  imageWidth,
}: {
  proxyHeight: number;
  proxyWidth: number;
  imageHeight: number;
  imageWidth: number;
}) {
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
}

const DropzoneLabel: React.FC<{ multiple?: boolean }> = ({ multiple }) => (
  <span className="zfe-files-ajax-dropzone-label">
    <span>
      Для загрузки перетащите
      {multiple ? ' файлы ' : ' файл '}в эту область.
    </span>
  </span>
);

interface FileAjaxElementProps {
  accept?: string;
  disabled?: boolean;
  files?: {}[];
  uploadBtnLabel?: string;
  maxChunkSize?: number;
  maxFileSize?: number;
  modelName?: string;
  multiple?: boolean;
  name: string;
  onLoaded?: () => unknown;
  previewRender?: () => unknown;
  schemaCode?: string;
  itemId?: number;
  type?: string;
  uploadUrl: string;
  linkUrl?: string;
  unlinkUrl?: string;
  form?: Element;
}

export const FileAjaxElement: React.FC<FileAjaxElementProps> = ({
  accept,
  disabled,
  files = [],
  uploadBtnLabel = 'Загрузить',
  maxChunkSize = 1024 ** 2,
  maxFileSize = 0,
  modelName,
  multiple,
  name,
  onLoaded = () => {},
  previewRender,
  schemaCode,
  itemId,
  type,
  uploadUrl,
  linkUrl,
  unlinkUrl,
  form,
  ...options
}) => {
  // eslint-disable-next-line object-curly-newline
  const [items, { addItem, updateItem, removeItem, getItem }] =
    useCollection(files);
  const [error, setError] = useState();

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

  const { getRootProps, getInputProps, isDragActive, open } = useDropzone({
    onDrop: React.useCallback(
      (acceptedFiles) => {
        acceptedFiles.forEach((file) => {
          if (!multiple) {
            if (items.filter((i) => !i.deleted).length) {
              // eslint-disable-next-line no-alert
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
              const { height: proxyHeight = null, width: proxyWidth = null } =
                options;

              const image = new Image();
              image.onload = () => {
                const { width: imageWidth, height: imageHeight } = image;

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
                context.drawImage(
                  image,
                  data.x,
                  data.y,
                  data.width,
                  data.height,
                  0,
                  0,
                  canvas.width,
                  canvas.height,
                );
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

          valid(
            file,
            options,
            () => {
              setError(undefined);
              const uploader = createUploader()
                .setUrl(uploadUrl)
                .setMaxChunkSize(maxChunkSize)
                .setFile(file)
                .setParams({ modelName, schemaCode, itemId })
                .onStart(() => pageUnload.disable(form))
                .onProgress(({ loaded, total }) =>
                  updateItem(item.key, {
                    uploadProgress: (loaded / total) * 100,
                  }),
                )
                .onComplete((raw) => {
                  const data = { data: {} };
                  Object.keys(raw).forEach((key) => {
                    if (/^data/.test(key)) {
                      const keyArr = /^data-(.*)/
                        [Symbol.replace](key, '$1')
                        .split('-');
                      const newKey = [
                        keyArr.shift(),
                        ...keyArr.map(
                          (k) =>
                            k.substr(0, 1).toUpperCase() +
                            k.substr(1).toLowerCase(),
                        ),
                      ].join('');
                      data.data[newKey] = raw[key];
                    } else {
                      data[key] = raw[key];
                    }
                  });
                  updateItem(item.key, { loading: false, ...data });
                  pageUnload.enable(form);
                })
                // eslint-disable-next-line no-console
                .onError((err) => {
                  setError(err.message);
                })
                .start();

              updateItem(item.key, {
                abortUpload: () => uploader.abort(),
                continueUpload: () => uploader.continue(),
              });
            },
            (message) => {
              // eslint-disable-next-line no-alert
              window.alert(message);
              removeItem(item.key);
            },
          );
        });
      },
      [items],
    ),
    noClick: true,
    multiple,
    disabled,
    accept: accept || getAcceptForType(type),
  });

  const openUploadWindow = useCallback(
    (e) => {
      open(e);

      if (!multiple) {
        items.map((item) => item.deleted && removeItem(item.key));
      }
    },
    [open, items, removeItem],
  );

  const cancelUpload = useCallback(
    (key) => {
      getItem(key).abortUpload();
      removeItem(key);
    },
    [getItem, removeItem],
  );

  const autoSave = itemId && modelName && schemaCode && unlinkUrl && linkUrl;

  const onDelete = useCallback(
    (key) => {
      updateItem(key, { deleted: true });

      if (autoSave) {
        const { id } = getItem(key);

        const formData = new FormData();
        formData.append('id', id);
        formData.append('model', modelName);
        formData.append('schema', schemaCode);
        formData.append('rel-id', itemId);

        fetch(unlinkUrl, {
          method: 'POST',
          cache: 'no-cache',
          body: formData,
        });
      }
    },
    [updateItem, getItem],
  );

  const onUndelete = useCallback(
    (key) => {
      updateItem(key, { deleted: null });

      if (autoSave) {
        const { id, data } = getItem(key);

        const formData = new FormData();
        formData.append('id', id);
        formData.append('model', modelName);
        formData.append('schema', schemaCode);
        formData.append('rel-id', itemId);
        Object.keys(data).forEach((prop) =>
          formData.append(`data[${prop}]`, data[prop]),
        );

        fetch(linkUrl, {
          method: 'POST',
          cache: 'no-cache',
          body: formData,
        });
      }
    },
    [updateItem],
  );

  const setData = useCallback(
    (key, data) => updateItem(key, { data }),
    [updateItem],
  );

  return (
    <div {...getRootProps()} className="zfe-files-ajax-dropzone">
      {error && (
        <div className="alert alert-danger">
          <button
            type="button"
            className="close"
            aria-label="Закрыть"
            onClick={() => {
              setError(undefined);
            }}
          >
            <span aria-hidden="true">&times;</span>
          </button>
          <strong>Ошибка:</strong> {error}
        </div>
      )}

      <input {...getInputProps()} />

      {isDragActive && <DropzoneLabel multiple={multiple} />}

      {(multiple || !items.filter((item) => !item.deleted).length) && (
        <button
          className="btn btn-default"
          type="button"
          disabled={disabled}
          onClick={openUploadWindow}
        >
          {uploadBtnLabel}
        </button>
      )}

      <FileAjaxPreview
        previewRender={previewRender}
        type={type}
        items={items}
        disabled={disabled}
        onDelete={onDelete}
        onUndelete={onUndelete}
        onCancelUpload={cancelUpload}
        setData={setData}
        {...options} // eslint-disable-line react/jsx-props-no-spreading
      />

      <Storage items={items} name={name} />
    </div>
  );
};
