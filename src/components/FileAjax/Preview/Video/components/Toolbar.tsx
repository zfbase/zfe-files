import Button from '../../Button';
import ButtonLink from '../../ButtonLink';
import QualityCheck from './QualityCheck';
import { VideoItem } from './Video';

interface VideoToolbarProps {
  item: VideoItem;
  disabled: boolean;
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
        title="Восстановить"
        onClick={() => onUndelete(item.key)}
        size="xs"
      />
    ) : (
      <Button
        icon="remove"
        title="Удалить"
        onClick={() => onDelete(item.key)}
        size="xs"
      />
    )}
  </div>
);

export default VideoToolbar;
