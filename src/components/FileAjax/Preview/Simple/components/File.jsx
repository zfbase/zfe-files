import React from 'react';
import PropTypes from 'prop-types';

import Icon from './Icon';
import Title from './Title';
import DownloadLink from './DownloadLink';
import Button from './Button';

const workingMessage = (percentage) => ((percentage < 100)
  ? `Загрузка… ${percentage ? `${percentage}%` : ''}`
  : 'Обработка…');

const File = ({ item, onDelete, onUndelete, onCancelUpload }) => (
  <li className={item.deleted && 'deleted'}>
    <Icon />
    <Title value={item.loading ? workingMessage(item.uploadProgress ? Math.round(item.uploadProgress) : null) : item.name} />
    {item.downloadUrl && <DownloadLink downloadUrl={item.downloadUrl} />}
    {item.deleted // eslint-disable-line no-nested-ternary
      ? <Button icon="repeat" title="Восстановить" onClick={() => onUndelete(item.key)} className="undelete" />
      : (item.loading
        ? <Button icon="remove" title="Отменить загрузку" onClick={() => onCancelUpload(item.key)} />
        : <Button icon="remove" title="Удалить" onClick={() => onDelete(item.key)} />
      )
    }
  </li>
);

File.propTypes = {
  item: PropTypes.shape({
    key: PropTypes.string,
    name: PropTypes.string,
    downloadUrl: PropTypes.string,
    deleted: PropTypes.bool,
    loading: PropTypes.bool,
    uploadProgress: PropTypes.number,
  }).isRequired,
  onDelete: PropTypes.func.isRequired,
  onUndelete: PropTypes.func.isRequired,
  onCancelUpload: PropTypes.func.isRequired,
};

export default File;
