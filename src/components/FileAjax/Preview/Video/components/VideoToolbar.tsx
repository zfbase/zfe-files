import { Button } from '../../Button';
import { ButtonLink } from '../../ButtonLink';
import { VideoFileItem } from '../VideoTypes';
import { VideoQualityCheck } from './VideoQualityCheck';

interface VideoToolbarProps {
  item: VideoFileItem;
  disabled?: boolean;
  onDelete: (key: string) => unknown;
  onUndelete: (key: string) => unknown;
}

export const VideoToolbar: React.FC<VideoToolbarProps> = ({
  item,
  disabled,
  onDelete,
  onUndelete,
}) => (
  <div
    className="btn-toolbar zfe-files-ajax-preview-video-toolbar"
    role="toolbar"
  >
    <VideoQualityCheck />
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
