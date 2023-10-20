import React from 'react';

import Toolbar from './Toolbar';
import Player from './Player';
import Frames from './Frames';

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

const Video: React.FC<VideoProps> = ({
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
      <Toolbar
        disabled={disabled}
        item={item}
        onDelete={onDelete}
        onUndelete={onUndelete}
      />
    </div>
    {item.previewUrl ? <Player src={item.previewUrl} /> : <Frames />}
  </div>
);

export default Video;
