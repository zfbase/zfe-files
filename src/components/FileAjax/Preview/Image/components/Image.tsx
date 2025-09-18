import { useMemo, useState } from 'react';
import {
  FaCropSimple,
  FaDownload,
  FaRotateLeft,
  FaXmark,
} from 'react-icons/fa6';
import { toInt } from '../../../utils/toInt';
import { Button } from '../../Button';
import { ButtonLink } from '../../ButtonLink';
import { FileImageData, FileImageItem } from '../ImageTypes';
import { AltButton } from './AltButton';
import { CropperLoader } from './cropper/CropperLoader';

export interface ImageProps {
  item: FileImageItem;
  disabled?: boolean;
  onDelete: (key: string) => void;
  onUndelete: (key: string) => void;
  setData: (key: string, data: Partial<FileImageData>) => void;
  width?: number | string;
  height?: number | string;
}

export const Image: React.FC<ImageProps> = ({
  item,
  disabled,
  onDelete,
  onUndelete,
  setData,
  width: w,
  height: h,
}) => {
  const [preview, setPreview] = useState<string>();
  const data = useMemo(() => {
    const d = item.data ? { ...item.data } : {};
    delete d.scaleX;
    delete d.scaleY;
    return d;
  }, [item.data]);

  const width = toInt(w);
  const height = toInt(h);

  const [cropperOpen, setCroppperOpen] = useState(false);

  return (
    <>
      <div className="zfe-files-ajax-preview-image">
        <div className="btn-toolbar" role="toolbar">
          <div className="btn-group" role="group">
            {typeof data.alt !== 'undefined' && (
              <AltButton
                disabled={item.deleted}
                data={data}
                setData={(data) => setData(item.key, data)}
              />
            )}
            {width && height && !disabled ? (
              <Button
                disabled={item.deleted}
                title="Кадрировать"
                label={<FaCropSimple />}
                onClick={() => setCroppperOpen(true)}
              />
            ) : null}
            {item.downloadUrl ? (
              <ButtonLink
                disabled={item.deleted}
                label={<FaDownload />}
                title="Скачать оригинал"
                url={item.downloadUrl}
              />
            ) : null}
            {disabled ? null : item.deleted ? (
              <Button
                label={<FaRotateLeft />}
                title="Восстановить"
                onClick={() => onUndelete(item.key)}
              />
            ) : (
              <Button
                label={<FaXmark />}
                title="Удалить"
                onClick={() => onDelete(item.key)}
              />
            )}
          </div>
        </div>

        <img
          className="zfe-files-ajax-preview-image-canvas"
          alt=""
          src={preview ?? item.previewUrl ?? item.previewLocal}
          style={{
            opacity: item.deleted ? 0.5 : 1,
            width: `${width}px`,
            aspectRatio: `${width}/${height}`,
          }}
        />

        <div className="img-border" />
      </div>

      {width && height && !disabled && cropperOpen ? (
        <CropperLoader
          aspectRatio={width && height ? width / height : undefined}
          data={data}
          onClose={() => setCroppperOpen(false)}
          setData={(data) => setData(item.key, data)}
          setPreview={setPreview}
          src={item.canvasUrl ?? item.downloadUrl ?? item.previewLocal}
        />
      ) : null}
    </>
  );
};
