import { useMemo, useState } from 'react';

import Button from '../../Button';
import ButtonLink from '../../ButtonLink';
import CropperModal from './CropperModal';
import AltButton from './AltButton';

export interface ImageData {
  scaleX: number;
  scaleY: number;
  alt?: string;
}

export interface ImageItem {
  canvasUrl: string;
  data?: ImageData;
  deleted: boolean;
  downloadUrl: string;
  key: string;
  loading: boolean;
  previewLocal: string;
  previewUrl: string;
  uploadProgress: number;
}

export interface ImageProps {
  item: ImageItem;
  disabled?: boolean;
  onDelete: (key: string) => void;
  onUndelete: (key: string) => void;
  setData: (data: ImageData) => void;
  width: number | string;
  height: number | string;
}

const Image: React.FC<ImageProps> = ({
  item,
  disabled,
  onDelete,
  onUndelete,
  setData,
  width,
  height,
}) => {
  const [preview, setPreview] = useState(null);

  const data = useMemo(() => {
    const { scaleX, scaleY, ...other } = item.data || {};
    return other;
  }, [item.data]);

  return (
    <div className="zfe-files-ajax-preview-image thumbnail">
      <div className="btn-toolbar" role="toolbar">
        {typeof item.data?.alt !== 'undefined' && (
          <AltButton data={data} setData={setData} />
        )}
        {width && height && !disabled ? (
          <CropperModal
            src={item.canvasUrl || item.downloadUrl || item.previewLocal}
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
            preview || item.previewUrl || item.previewLocal
          })`,
          opacity: item.deleted ? 0.5 : 1,
          width: `${width || 200}px`,
          height: `${height || 200}px`,
        }}
      />
    </div>
  );
};

export default Image;
