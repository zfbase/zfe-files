import { Fragment } from 'react';
import { Button, DownloadLink } from '../../Simple';
import usePlayer from '../utils/usePlayer';
import ControlBtn from './ControlBtn';
import Time from './Time';
import Title from './Title';

export interface AudioItem {
  key: string;
  name: string;
  duration: number;
  downloadUrl: string;
  previewUrl: string;
  deleted: boolean;
  loading: boolean;
}

export interface AudioProps {
  item: AudioItem;
  disabled: boolean;
  onDelete: (key: string) => void;
  onUndelete: (key: string) => void;
  Wrapper: string | React.ElementType;
}

const Audio: React.FC<AudioProps> = ({
  item,
  disabled,
  onDelete,
  onUndelete,
  Wrapper = 'div',
}) => {
  const { Player, play, pause, state } = usePlayer(item.previewUrl);

  return (
    <Fragment>
      <Player />
      <Wrapper className={item.deleted && 'deleted'}>
        {state === 'play' ? (
          <ControlBtn icon="pause" onClick={pause} />
        ) : (
          <ControlBtn icon="play" onClick={play} />
        )}
        <Title label={item.loading ? 'Загрузка…' : item.name} />
        {item.downloadUrl && <DownloadLink downloadUrl={item.downloadUrl} />}
        {item.duration && <Time value={item.duration} />}
        {disabled ? null : item.deleted ? (
          <Button
            icon="repeat"
            title="Восстановить"
            onClick={() => onUndelete(item.key)}
            className="undelete"
          />
        ) : (
          <Button
            icon="remove"
            title="Удалить"
            onClick={() => onDelete(item.key)}
          />
        )}
      </Wrapper>
    </Fragment>
  );
};

export default Audio;
