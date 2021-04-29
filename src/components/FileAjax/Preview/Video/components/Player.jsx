import React from 'react';
import PropTypes from 'prop-types';

const Player = ({ src }) => (
  <video
    src={src}
    controls
    className="zfe-files-ajax-preview-video-player"
  />
);

Player.propTypes = {
  src: PropTypes.string
};

export default Player;
