import { useMemo, useState } from 'react';

import Button from '../../Button';
import ButtonLink from '../../ButtonLink';
import CropperModal from './CropperModal';
import AltButton from './AltButton';

export interface ImageData {
  alt?: string;
  scaleX: number;
  scaleY: number;
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
  disabled?: boolean;
  height: number | string;
  item: ImageItem;
  onDelete: (key: string) => void;
  onUndelete: (key: string) => void;
  setData: (data: ImageData) => void;
  width: number | string;
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
            data={data}
            height={height}
            setData={setData}
            setPreview={setPreview}
            src={item.canvasUrl || item.downloadUrl || item.previewLocal}
            width={width}
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
            onClick={() => onUndelete(item.key)}
            size="xs"
            title="Восстановить"
          />
        ) : (
          <Button
            icon="remove"
            onClick={() => onDelete(item.key)}
            size="xs"
            title="Удалить"
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
