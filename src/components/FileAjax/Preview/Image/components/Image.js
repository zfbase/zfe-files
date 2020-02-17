import React from 'react';
import PropTypes from 'prop-types';

import Button from './Button';

const Image = ({ item, onDelete, onUndelete }) => (
  <div className="zfe-files-ajax-preview-image thumbnail">
    <div className="btn-toolbar" role="toolbar">
      {item.downloadUrl && <Button icon="download-alt" title="Скачать оригинал" />}
      {item.deleted
        ? <Button icon="repeat" title="Восстановить" onClick={() => onUndelete(item.key)} />
        : <Button icon="remove" title="Удалить" onClick={() => onDelete(item.key)} />}
    </div>
    <img src={item.previewUrl} style={{ opacity: item.deleted ? 0.5 : 1}} />
  </div>
);

Image.propTypes = {
  item: PropTypes.shape({
    key: PropTypes.string,
    downloadUrl: PropTypes.string,
    previewUrl: PropTypes.string,
    deleted: PropTypes.bool,
  }).isRequired,
  onDelete: PropTypes.func.isRequired,
  onUndelete: PropTypes.func.isRequired,
};

export default Image;
