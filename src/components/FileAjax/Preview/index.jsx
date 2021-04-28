import React from 'react';
import PropTypes from 'prop-types';

import AudioPreview from './Audio/index';
import ImagePreview from './Image/index';
import SimplePreview from './Simple/index';
// import VideoPreview from './Video/index';

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

  // eslint-disable-next-line react/jsx-props-no-spreading
  return <Helper {...props} />;
};

Preview.propTypes = {
  previewRender: PropTypes.element,
  type: PropTypes.string,
};

Preview.defaultProps = {
  previewRender: null,
  type: null,
};

export default Preview;
