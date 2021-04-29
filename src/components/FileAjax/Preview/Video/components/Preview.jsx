import React from 'react';
import PropTypes from 'prop-types';

import Video from './Video';
import Loader from './Loader';

const Preview = ({ items, ...props }) => (
  <div className="zfe-files-ajax-preview-video-wrap">
    {items.map(item => React.createElement(
      item.loading ? Loader : Video,
      { item, ...props, key: item.key },
    ))}
  </div>
);

Preview.propTypes = {
  items: PropTypes.arrayOf(PropTypes.shape({
    key: PropTypes.string,
    loading: PropTypes.bool,
  })).isRequired,
};

export default Preview;
