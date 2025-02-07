import { Button } from '../../Button';
import { VideoFileItem } from '../VideoTypes';

export interface VideoLoadingProps {
  item: VideoFileItem;
  onCancelUpload: (key: string) => unknown;
}

export const VideoLoading: React.FC<VideoLoadingProps> = ({
  item,
  onCancelUpload,
}) => {
  const percentage = item.uploadProgress ? Math.round(item.uploadProgress) : 0;
  return (
    <div className="zfe-files-ajax-preview-video">
      <div className="zfe-files-ajax-preview-video-header">
        <div className="zfe-files-ajax-preview-video-info">
          {percentage < 100 ? 'Загрузка…' : 'Обработка…'}
        </div>
        <div className="btn-toolbar zfe-files-ajax-preview-video-toolbar">
          <Button
            icon="remove"
            title="Отменить загрузку"
            onClick={() => onCancelUpload(item.key)}
            size="xs"
          />
        </div>
      </div>
      <div className="progress">
        <div
          className="progress-bar"
          role="progressbar"
          style={{ width: `${percentage}%` }}
        >
          {percentage}%
        </div>
      </div>
    </div>
  );
};
