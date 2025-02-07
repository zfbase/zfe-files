import { ElementType } from 'react';
import { SimpleButton } from '../../Simple/components/SimpleButton';
import { SimpleDownloadLink } from '../../Simple/components/SimpleDownloadLink';
import { AudioFileItem } from '../AudioTypes';
import { usePlayer } from '../utils/usePlayer';
import { AudioControlBtn } from './AudioControlBtn';
import { AudioTime } from './AudioTime';
import { AudioTitle } from './AudioTitle';

export interface AudioProps {
  item: AudioFileItem;
  disabled?: boolean;
  onDelete: (key: string) => unknown;
  onUndelete: (key: string) => unknown;
  Wrapper?: string | ElementType;
}

export const Audio: React.FC<AudioProps> = ({
  item,
  disabled,
  onDelete,
  onUndelete,
  Wrapper = 'div',
}) => {
  const { ref, play, pause, state } = usePlayer();

  return (
    <>
      <audio ref={ref} src={item.previewUrl} />

      <Wrapper className={item.deleted && 'deleted'}>
        {state === 'play' ? (
          <AudioControlBtn icon="pause" onClick={pause} />
        ) : (
          <AudioControlBtn icon="play" onClick={play} />
        )}
        <AudioTitle label={item.loading ? 'Загрузка…' : item.name} />
        {item.downloadUrl && (
          <SimpleDownloadLink downloadUrl={item.downloadUrl} />
        )}
        {item.duration && <AudioTime value={item.duration} />}
        {disabled ? null : item.deleted ? (
          <SimpleButton
            icon="repeat"
            title="Восстановить"
            onClick={() => onUndelete(item.key)}
            className="undelete"
          />
        ) : (
          <SimpleButton
            icon="remove"
            title="Удалить"
            onClick={() => onDelete(item.key)}
          />
        )}
      </Wrapper>
    </>
  );
};
