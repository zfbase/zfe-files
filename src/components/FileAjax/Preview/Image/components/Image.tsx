import { useMemo, useState } from 'react';
import { Button } from '../../Button';
import { ButtonLink } from '../../ButtonLink';
import { FileImageData, FileImageItem } from '../ImageTypes';
import { AltButton } from './AltButton';
import { CropperModal } from './CropperModal';

export interface ImageProps {
  item: FileImageItem;
  disabled?: boolean;
  onDelete: (key: string) => void;
  onUndelete: (key: string) => void;
  setData: (data: FileImageData) => void;
  width?: number | string;
  height?: number | string;
}

export const Image: React.FC<ImageProps> = ({
  item,
  disabled,
  onDelete,
  onUndelete,
  setData,
  width,
  height,
}) => {
  const [preview, setPreview] = useState<string>();
  const data = useMemo(() => {
    const { scaleX, scaleY, ...other } = item.data ?? {};
    return other;
  }, [item.data]);
  return (
    <div className="zfe-files-ajax-preview-image thumbnail">
      <div className="btn-toolbar" role="toolbar">
        {typeof data.alt !== 'undefined' && (
          <AltButton data={data} setData={setData} />
        )}
        {width && height && !disabled ? (
          <CropperModal
            src={item.canvasUrl ?? item.downloadUrl ?? item.previewLocal}
            width={width}
            height={height}
            data={data}
            setData={setData}
            setPreview={setPreview}
          />
        ) : null}
        {item.downloadUrl ? (
          <ButtonLink
            icon="download-alt"
            title="Скачать оригинал"
            url={item.downloadUrl}
          />
        ) : null}
        {disabled ? null : item.deleted ? (
          <Button
            icon="repeat"
            title="Восстановить"
            onClick={() => onUndelete(item.key)}
            size="xs"
          />
        ) : (
          <Button
            icon="remove"
            title="Удалить"
            onClick={() => onDelete(item.key)}
            size="xs"
          />
        )}
      </div>
      <div
        className="zfe-files-ajax-preview-image-canvas"
        style={{
          backgroundImage: `url(${
            preview ?? item.previewUrl ?? item.previewLocal
          })`,
          opacity: item.deleted ? 0.5 : 1,
          width: `${width ?? 200}px`,
          height: `${height ?? 200}px`,
        }}
      />
    </div>
  );
};
