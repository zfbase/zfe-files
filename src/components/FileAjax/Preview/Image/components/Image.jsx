import React, { useMemo, useState } from 'react';
import PropTypes from 'prop-types';

import Button from '../../Button';
import ButtonLink from '../../ButtonLink';
import CropperModal from './CropperModal';

const Image = ({
  item,
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
        {width && height ? (
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
        {item.deleted
          ? <Button icon="repeat" title="Восстановить" onClick={() => onUndelete(item.key)} size="xs" />
          : <Button icon="remove" title="Удалить" onClick={() => onDelete(item.key)} size="xs" />}
      </div>
      <div
        className="zfe-files-ajax-preview-image-canvas"
        style={{
          backgroundImage: `url(${preview || item.previewUrl || item.previewLocal})`,
          opacity: item.deleted ? 0.5 : 1,
          width: `${width || 200}px`,
          height: `${height || 200}px`,
        }}
      />
    </div>
  );
};

Image.propTypes = {
  item: PropTypes.shape({
    key: PropTypes.string,
    canvasUrl: PropTypes.string,
    downloadUrl: PropTypes.string,
    previewUrl: PropTypes.string,
    previewLocal: PropTypes.string,
    deleted: PropTypes.bool,
    data: PropTypes.object,
  }).isRequired,
  onDelete: PropTypes.func.isRequired,
  onUndelete: PropTypes.func.isRequired,
  setData: PropTypes.func.isRequired,
  width: PropTypes.oneOfType([
    PropTypes.number,
    PropTypes.string,
  ]),
  height: PropTypes.oneOfType([
    PropTypes.number,
    PropTypes.string,
  ]),
};

Image.defaultProps = {
  width: null,
  height: null,
};

export default Image;
