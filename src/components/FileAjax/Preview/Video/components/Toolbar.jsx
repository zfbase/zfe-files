import React from 'react';
import PropTypes from 'prop-types';

import Button from '../../Button';
import ButtonLink from '../../ButtonLink';
import QualityCheck from './QualityCheck';

const Toolbar = ({ item, disabled, onDelete, onUndelete }) => (
  <div className="btn-toolbar zfe-files-ajax-preview-video-toolbar" role="toolbar">
    <QualityCheck />
    {item.downloadUrl ? (
      <ButtonLink
        icon="download-alt"
        title="Скачать оригинал"
        url={item.downloadUrl}
      />
    ) : null}
    {disabled ? null : (
      item.deleted
        ? <Button icon="repeat" title="Восстановить" onClick={() => onUndelete(item.key)} size="xs" />
        : <Button icon="remove" title="Удалить" onClick={() => onDelete(item.key)} size="xs" />
    )}
  </div>
);

Toolbar.propTypes = {
  item: PropTypes.shape({
    downloadUrl: PropTypes.string,
    deleted: PropTypes.bool,
    key: PropTypes.string,
  }).isRequired,
  disabled: PropTypes.bool,
  onDelete: PropTypes.func.isRequired,
  onUndelete: PropTypes.func.isRequired,
};

Toolbar.defaultProps = {
  disabled: false,
};

export default Toolbar;
