import React from 'react';
import PropTypes from 'prop-types';

import Audio from './Audio';

const Preview = ({ items, ...props }) => (
  <ul className="zfe-files-ajax-preview-simple">
    {items.map(item => <Audio {...{ item, ...props }} Wrapper="li" key={item.key} />)}
  </ul>
);

Preview.propTypes = {
  items: PropTypes.array.isRequired,
};

export default Preview;
