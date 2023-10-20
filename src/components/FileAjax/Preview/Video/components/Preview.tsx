import VideoLoader, { type VideoLoaderProps } from './Loader';
import Video, { type VideoItem, type VideoProps } from './Video';

type VideoPreviewProps = {
  items: VideoItem[];
} & Omit<VideoProps, 'item'> &
  Pick<VideoLoaderProps, 'onCancelUpload'>;

const VideoPreview: React.FC<VideoPreviewProps> = ({
  items,
  onCancelUpload,
  ...props
}) => (
  <div className="zfe-files-ajax-preview-video-wrap">
    {items.map((item) =>
      item.loading ? (
        <VideoLoader
          onCancelUpload={onCancelUpload}
          item={item}
          key={item.key}
        />
      ) : (
        <Video item={item} key={item.key} {...props} />
      ),
    )}
  </div>
);

export default VideoPreview;
