import React from 'react';
import PropTypes from 'prop-types';

import Audio from './Audio';

const Preview = ({ items, ...props }) => (
  <ul className="zfe-files-ajax-preview-simple">
    {/* eslint-disable-next-line react/jsx-props-no-spreading */}
    {items.map((item) => <Audio item={item} {...props} Wrapper="li" key={item.key} />)}
  </ul>
);

Preview.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  items: PropTypes.array.isRequired,
};

export default Preview;
