import { Button } from '../Button';
import type { VideoItem } from './Video';

export interface VideoLoaderProps {
  item: VideoItem;
  onCancelUpload: (key: string) => void;
}

export const VideoLoader: React.FC<VideoLoaderProps> = ({
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
            onClick={() => onCancelUpload(item.key)}
            size="xs"
            title="Отменить загрузку"
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
