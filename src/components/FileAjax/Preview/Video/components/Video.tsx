import { VideoToolbar } from './VideoToolbar';
import { VideoPlayer } from './VideoPlayer';
import { VideoFrames } from './VideoFrames';
import { VideoFileItem } from '../VideoTypes';

export interface VideoProps {
  item: VideoFileItem;
  disabled?: boolean;
  onDelete: () => unknown;
  onUndelete: () => unknown;
}

export const Video: React.FC<VideoProps> = ({
  item,
  disabled,
  onDelete,
  onUndelete,
}) => (
  <div className="zfe-files-ajax-preview-video">
    <div className="zfe-files-ajax-preview-video-header">
      <div className="zfe-files-ajax-preview-video-name">{item.name}</div>
      {item.size ? (
        <div className="zfe-files-ajax-preview-video-info">{item.size}</div>
      ) : null}
      {item.duration ? (
        <div className="zfe-files-ajax-preview-video-info">{item.duration}</div>
      ) : null}
      <VideoToolbar
        item={item}
        disabled={disabled}
        onDelete={onDelete}
        onUndelete={onUndelete}
      />
    </div>
    {item.previewUrl ? <VideoPlayer src={item.previewUrl} /> : <VideoFrames />}
  </div>
);
