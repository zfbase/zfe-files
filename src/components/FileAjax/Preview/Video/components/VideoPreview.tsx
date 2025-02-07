import { VideoFileItem } from '../VideoTypes';
import { Video, VideoProps } from './Video';
import { VideoLoading, VideoLoadingProps } from './VideoLoading';

type VideoPreviewProps = {
  items: VideoFileItem[];
} & Omit<VideoProps & VideoLoadingProps, 'item'>;

export const VideoPreview: React.FC<VideoPreviewProps> = ({
  items,
  ...rest
}) => (
  <div className="zfe-files-ajax-preview-video-wrap">
    {items.map((item) =>
      item.loading ? (
        <VideoLoading key={item.key} item={item} {...rest} />
      ) : (
        <Video key={item.key} item={item} {...rest} />
      ),
    )}
  </div>
);
