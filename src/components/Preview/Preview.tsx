import { AudioPreview } from './Audio/AudioPreview';
import { ImagePreview } from './Image/ImagePreview';
import { SimplePreview } from './Simple/SimplePreview';
import { VideoPreview } from './Video/VideoPreview';

interface CommonPreviewProps {}

interface PreviewProps {
  PreviewComponent?: React.FC<CommonPreviewProps>;
  type?: string;
}

export const Preview: React.FC<PreviewProps & CommonPreviewProps> = ({
  PreviewComponent,
  type,
  ...props
}) => {
  if (PreviewComponent) {
    return <PreviewComponent {...props} />;
  }
  switch (type) {
    case 'image':
      return <ImagePreview {...props} />;
    case 'audio':
      return <AudioPreview {...props} />;
    case 'video':
      return <VideoPreview {...props} />;
    default:
      return <SimplePreview {...props} />;
  }
};
