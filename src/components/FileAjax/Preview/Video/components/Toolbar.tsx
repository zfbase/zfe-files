import Button from '../../Button';
import ButtonLink from '../../ButtonLink';
import QualityCheck from './QualityCheck';
import { VideoItem } from './Video';

interface VideoToolbarProps {
  disabled: boolean;
  item: VideoItem;
  onDelete: (key: string) => void;
  onUndelete: (key: string) => void;
}

const VideoToolbar: React.FC<VideoToolbarProps> = ({
  item,
  disabled,
  onDelete,
  onUndelete,
}) => (
  <div
    className="btn-toolbar zfe-files-ajax-preview-video-toolbar"
    role="toolbar"
  >
    <QualityCheck />
    {item.downloadUrl ? (
      <ButtonLink
        icon="download-alt"
        title="Скачать оригинал"
        url={item.downloadUrl}
      />
    ) : null}
    {disabled ? null : item.deleted ? (
      <Button
        icon="repeat"
        onClick={() => onUndelete(item.key)}
        size="xs"
        title="Восстановить"
      />
    ) : (
      <Button
        icon="remove"
        onClick={() => onDelete(item.key)}
        size="xs"
        title="Удалить"
      />
    )}
  </div>
);

export default VideoToolbar;
