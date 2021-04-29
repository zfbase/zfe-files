import React from 'react';

import Toolbar from './Toolbar';
import Player from './Player';
import Frames from './Frames';

const Video = ({ item, onDelete, onUndelete }) => (
  <div className="zfe-files-ajax-preview-video">
    <div className="zfe-files-ajax-preview-video-header">
      <div className="zfe-files-ajax-preview-video-info">
        {item.name}
      </div>
      {item.size ? <div>{item.size}</div> : null}
      <Toolbar
        item={item}
        onDelete={onDelete}
        onUndelete={onUndelete}
      />
    </div>
    {item.previewUrl ? <Player src={item.previewUrl} /> : <Frames />}
  </div>
);

export default Video;
