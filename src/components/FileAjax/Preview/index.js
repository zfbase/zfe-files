import React from 'react';

import AudioPreview from './Audio/index';
import ImagePreview from './Image/index';
import SimplePreview from './Simple/index';
import VideoPreview from './Video/index';

const Preview = ({ previewRender, type, ...props }) => {
  let Helper;
  if (React.isValidElement(previewRender)) {
    Helper = previewRender;
  } else {
    switch (type) {
      case 'image': Helper = ImagePreview; break;
      case 'audio': Helper = AudioPreview; break;
      // case 'video': Helper = VideoPreview; break;
      default: Helper = SimplePreview; break;
    }
  }
  return <Helper {...props} />
};

export default Preview;
