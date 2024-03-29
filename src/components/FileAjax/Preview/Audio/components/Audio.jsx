import React, { Fragment } from 'react';
import PropTypes from 'prop-types';

import usePlayer from '../utils/usePlayer';
import ControlBtn from './ControlBtn';
import Title from './Title';
import Time from './Time';
import { Button, DownloadLink } from '../../Simple';

const Audio = ({
  item,
  disabled,
  onDelete,
  onUndelete,
  Wrapper,
}) => {
  const {
    Player,
    play,
    pause,
    state,
  } = usePlayer(item.previewUrl);

  return (
    <Fragment>
      <Player />
      <Wrapper className={item.deleted && 'deleted'}>
        {state === 'play'
          ? <ControlBtn icon="pause" onClick={pause} />
          : <ControlBtn icon="play" onClick={play} />}
        <Title value={item.loading ? 'Загрузка…' : item.name} />
        {item.downloadUrl && <DownloadLink downloadUrl={item.downloadUrl} />}
        {item.duration && <Time value={item.duration} />}
        {disabled ? null : (
          item.deleted
            ? <Button icon="repeat" title="Восстановить" onClick={() => onUndelete(item.key)} className="undelete" />
            : <Button icon="remove" title="Удалить" onClick={() => onDelete(item.key)} />
        )}
      </Wrapper>
    </Fragment>
  );
};

Audio.propTypes = {
  item: PropTypes.shape({
    key: PropTypes.string,
    name: PropTypes.string,
    duration: PropTypes.number,
    downloadUrl: PropTypes.string,
    previewUrl: PropTypes.string,
    deleted: PropTypes.bool,
    loading: PropTypes.bool,
  }).isRequired,
  disabled: PropTypes.bool,
  onDelete: PropTypes.func.isRequired,
  onUndelete: PropTypes.func.isRequired,
  Wrapper: PropTypes.oneOfType([
    PropTypes.string,
    PropTypes.element,
  ]),
};

Audio.defaultProps = {
  Wrapper: 'div',
  disabled: false,
};

export default Audio;
