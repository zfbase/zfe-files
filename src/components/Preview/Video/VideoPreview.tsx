import { Video, type VideoItem, type VideoProps } from './Video';
import { VideoLoader, type VideoLoaderProps } from './VideoLoader';

type VideoPreviewProps = {
  items: VideoItem[];
} & Omit<VideoProps, 'item'> &
  Pick<VideoLoaderProps, 'onCancelUpload'>;

export const VideoPreview: React.FC<VideoPreviewProps> = ({
  items,
  onCancelUpload,
  ...props
}) => (
  <div className="zfe-files-ajax-preview-video-wrap">
    {items.map((item) =>
      item.loading ? (
        <VideoLoader
          item={item}
          key={item.key}
          onCancelUpload={onCancelUpload}
        />
      ) : (
        <Video item={item} key={item.key} {...props} />
      ),
    )}
  </div>
);
