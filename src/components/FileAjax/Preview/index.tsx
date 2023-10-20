import { ReactNode } from 'react';

import AudioPreview from './Audio/index';
import ImagePreview from './Image/index';
import SimplePreview from './Simple/index';
import VideoPreview from './Video/index';

interface PreviewProps {
  previewRender?: () => ReactNode;
  type?: string;
}

const Preview: React.FC<PreviewProps> = ({ previewRender, type, ...props }) => {
  let Helper: React.FC;
  if (typeof previewRender == 'function') {
    Helper = previewRender;
  } else {
    switch (type) {
      case 'image':
        Helper = ImagePreview;
        break;
      case 'audio':
        Helper = AudioPreview;
        break;
      case 'video':
        Helper = VideoPreview;
        break;
      default:
        Helper = SimplePreview;
        break;
    }
  }

  // eslint-disable-next-line react/jsx-props-no-spreading
  return <Helper {...props} />;
};

export default Preview;
