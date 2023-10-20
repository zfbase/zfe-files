import { Fragment } from 'react';
import { Button } from '../Button';
import { DownloadLink } from '../Simple/DownloadLink';
import { AudioTitle } from './AudioTitle';
import { ControlBtn } from './ControlBtn';
import { Time } from './Time';
import { usePlayer } from './usePlayer';

export interface AudioItem {
  deleted: boolean;
  downloadUrl: string;
  duration: number;
  key: string;
  loading: boolean;
  name: string;
  previewUrl: string;
}

export interface AudioProps {
  Wrapper: string | React.ElementType;
  disabled: boolean;
  item: AudioItem;
  onDelete: (key: string) => void;
  onUndelete: (key: string) => void;
}

export const Audio: React.FC<AudioProps> = ({
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
        <AudioTitle label={item.loading ? 'Загрузка…' : item.name} />
        {item.downloadUrl && <DownloadLink downloadUrl={item.downloadUrl} />}
        {item.duration && <Time value={item.duration} />}
        {disabled ? null : item.deleted ? (
          <Button
            className="undelete"
            icon="repeat"
            onClick={() => onUndelete(item.key)}
            title="Восстановить"
          />
        ) : (
          <Button
            icon="remove"
            onClick={() => onDelete(item.key)}
            title="Удалить"
          />
        )}
      </Wrapper>
    </Fragment>
  );
};
