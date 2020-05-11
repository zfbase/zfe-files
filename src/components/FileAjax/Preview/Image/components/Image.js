import React from 'react';
import PropTypes from 'prop-types';

import Button from './Button';
import ButtonLink from './ButtonLink';

const Image = ({ item, onDelete, onUndelete }) => (
  <div className="zfe-files-ajax-preview-image thumbnail">
    <div className="btn-toolbar" role="toolbar">
      {item.downloadUrl ? (
        <ButtonLink
          icon="download-alt"
          title="Скачать оригинал"
          url={item.downloadUrl}
        />
      ) : null}
      {item.deleted
        ? <Button icon="repeat" title="Восстановить" onClick={() => onUndelete(item.key)} />
        : <Button icon="remove" title="Удалить" onClick={() => onDelete(item.key)} />}
    </div>
    <img src={item.previewUrl || item.previewLocal} style={{ opacity: item.deleted ? 0.5 : 1 }} alt="" />
  </div>
);

Image.propTypes = {
  item: PropTypes.shape({
    key: PropTypes.string,
    downloadUrl: PropTypes.string,
    previewUrl: PropTypes.string,
    previewLocal: PropTypes.string,
    deleted: PropTypes.bool,
  }).isRequired,
  onDelete: PropTypes.func.isRequired,
  onUndelete: PropTypes.func.isRequired,
};

export default Image;
