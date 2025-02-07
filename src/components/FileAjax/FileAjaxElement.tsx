import {
  MouseEventHandler,
  ReactNode,
  useEffect,
  useMemo,
  useState,
} from 'react';
import { useDropzone } from 'react-dropzone';
import { FileItem, FileOptions } from '../../CommonTypes';
import { useCollection } from '../../hooks/useCollection';
import { createUploader } from '../../utils/createUploader';
import { pageUnload } from '../../utils/pageUnload';
import { validImageMinSize } from '../../validators/images/validImageMinSize';
import { DropzoneLabel } from './DropzoneLabel';
import { FileAjaxPreview } from './Preview/FileAjaxPreview';
import { Storage } from './Storage';
import { getAcceptForType } from './utils/getAcceptorForType';
import { getImageBox } from './utils/getImageBox';

type FileAjaxElementProps = {
  accept?: string;
  disabled?: boolean;
  files?: FileItem[];
  uploadBtnLabel?: string;
  maxChunkSize?: number;
  maxFileSize?: number;
  modelName?: string;
  multiple?: boolean;
  name: string;
  onLoaded?: () => unknown;
  previewRender?: () => ReactNode;
  schemaCode?: string;
  itemId?: number;
  type?: 'audio' | 'image' | 'video';
  uploadUrl: string;
  linkUrl?: string;
  unlinkUrl?: string;
  form?: HTMLFormElement;
} & FileOptions;

export const FileAjaxElement: React.FC<FileAjaxElementProps> = ({
  accept,
  disabled = false,
  files = [],
  uploadBtnLabel = 'Загрузить',
  maxChunkSize = 1024 ** 2,
  maxFileSize = 0,
  modelName,
  multiple = false,
  name,
  onLoaded,
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
  const [error, setError] = useState<string>();

  // Надо использовать валидаторы, указанные в элементе формы
  const valid: typeof validImageMinSize = (file, settings, success, fail) => {
    if (type === 'image') {
      validImageMinSize(file, settings, success, fail);
    } else {
      success();
    }
  };

  useEffect(() => {
    if (onLoaded) {
      onLoaded();
    }
  }, [onLoaded]);

  const { getRootProps, getInputProps, isDragActive, open } = useDropzone({
    onDrop: useMemo(
      () => (acceptedFiles: File[]) => {
        acceptedFiles.forEach((file) => {
          if (!multiple) {
            if (items.filter((i) => !i.deleted).length) {
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
                if (!proxyWidth || !proxyHeight) {
                  return;
                }

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
                if (context) {
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
                }

                updateItem(item.key, {
                  data,
                  previewLocal: canvas.toDataURL('image/jpg'),
                });
              };
              if (typeof reader.result === 'string') {
                image.src = reader.result;
              }

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
                .onStart(() => {
                  if (form) {
                    pageUnload.disable(form);
                  }
                })
                .onProgress(({ loaded, total }) =>
                  updateItem(item.key, {
                    uploadProgress: (loaded / total) * 100,
                  }),
                )
                .onComplete((raw) => {
                  const data: {
                    data: NonNullable<FileItem['data']>;
                  } = {
                    data: {},
                  };
                  Object.keys(raw).forEach((key) => {
                    if (/^data/.test(key)) {
                      const keyArr = /^data-(.*)/
                        [Symbol.replace](key, '$1')
                        .split('-');
                      const newKey = [
                        keyArr.shift(),
                        ...keyArr.map(
                          (k) =>
                            k.substring(0, 1).toUpperCase() +
                            k.substring(1).toLowerCase(),
                        ),
                      ].join('');
                      data.data[newKey] = raw[key];
                    } else {
                      data[key] = raw[key];
                    }
                  });
                  updateItem(item.key, { loading: false, ...data });
                  if (form) {
                    pageUnload.enable(form);
                  }
                })
                // eslint-disable-next-line no-console
                .onError((err) => {
                  if (err instanceof Error) {
                    setError(err.message);
                  }
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
    accept: accept ?? getAcceptForType(type),
  });
  const openUploadWindow = useMemo<MouseEventHandler>(
    () => () => {
      open();

      if (!multiple) {
        items.map((item) => item.deleted && removeItem(item.key));
      }
    },
    [open, items, removeItem],
  );

  const cancelUpload = useMemo(
    () => (key: string) => {
      const item = getItem(key);
      if (item && item.abortUpload) {
        item.abortUpload();
      }
      removeItem(key);
    },
    [getItem, removeItem],
  );

  const autoSave = !!(
    itemId &&
    modelName &&
    schemaCode &&
    unlinkUrl &&
    linkUrl
  );

  const onDelete = useMemo(
    () => (key: string) => {
      updateItem(key, { deleted: true });

      if (autoSave) {
        const item = getItem(key);

        if (item) {
          const formData = new FormData();
          formData.append('id', `${item.id}`);
          formData.append('model', modelName);
          formData.append('schema', schemaCode);
          formData.append('rel-id', itemId.toString());

          fetch(unlinkUrl, {
            method: 'POST',
            cache: 'no-cache',
            body: formData,
          });
        }
      }
    },
    [updateItem, getItem],
  );

  const onUndelete = useMemo(
    () => (key: string) => {
      updateItem(key, { deleted: false });

      if (autoSave) {
        const item = getItem(key);
        if (item) {
          const formData = new FormData();
          formData.append('id', `${item.id}`);
          formData.append('model', modelName);
          formData.append('schema', schemaCode);
          formData.append('rel-id', itemId.toString());
          if (item.data) {
            Object.entries(item.data).forEach(([key, value]) =>
              formData.append(`data[${key}]`, `${value}`),
            );
          }

          fetch(linkUrl, {
            method: 'POST',
            cache: 'no-cache',
            body: formData,
          });
        }
      }
    },
    [updateItem],
  );

  const setData = useMemo(
    () => (key: string, data: FileItem['data']) => updateItem(key, { data }),
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
        {...options}
      />

      <Storage items={items} name={name} />
    </div>
  );
};
