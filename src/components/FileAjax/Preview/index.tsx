import AudioPreview from './Audio/index';
import ImagePreview from './Image/index';
import SimplePreview from './Simple/index';
import VideoPreview from './Video/index';

interface CommonPreviewProps {}

interface PreviewProps {
  PreviewComponent?: React.FC<CommonPreviewProps>;
  type?: string;
}

const Preview: React.FC<PreviewProps & CommonPreviewProps> = ({
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

export default Preview;
