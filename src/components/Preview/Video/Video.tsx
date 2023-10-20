import { VideoToolbar } from './VideoToolbar';
import { VideoFrames } from './VideoFrames';
import { VideoPlayer } from './VideoPlayer';

export interface VideoItem {
  deleted: boolean;
  downloadUrl: string;
  duration: string;
  key: string;
  loading: boolean;
  name: string;
  previewUrl: string;
  size: string;
  uploadProgress: number;
}

export interface VideoProps {
  disabled: boolean;
  item: VideoItem;
  onDelete: (key: string) => void;
  onUndelete: (key: string) => void;
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
        disabled={disabled}
        item={item}
        onDelete={onDelete}
        onUndelete={onUndelete}
      />
    </div>
    {item.previewUrl ? <VideoPlayer src={item.previewUrl} /> : <VideoFrames />}
  </div>
);
