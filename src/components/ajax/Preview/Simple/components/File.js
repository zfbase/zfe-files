import React from 'react';
import PropTypes from 'prop-types';

import Icon from './Icon';
import Title from './Title';
import DownloadLink from './DownloadLink';
import Button from './Button';

const File = ({ item, onDelete, onUndelete }) => (
  <li className={item.deleted && 'deleted'}>
    <Icon />
    <Title value={item.loading ? 'Загрузка…' : item.name} />
    {item.downloadUrl && <DownloadLink downloadUrl={item.downloadUrl} />}
    {item.deleted
        ? <Button icon="repeat" title="Восстановить" onClick={() => onUndelete(item.key)} className="undelete" />
        : <Button icon="remove" title="Удалить" onClick={() => onDelete(item.key)} />}
  </li>
);

File.propTypes = {
  item: PropTypes.shape({
    key: PropTypes.string,
    name: PropTypes.string,
    downloadUrl: PropTypes.string,
    deleted: PropTypes.bool,
    loading: PropTypes.bool,
  }).isRequired,
  onDelete: PropTypes.func.isRequired,
  onUndelete: PropTypes.func.isRequired,
};

export default File;
