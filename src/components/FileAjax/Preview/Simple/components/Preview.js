import React from 'react';
import PropTypes from 'prop-types';

import File from './File';

const Preview = ({ items, ...props }) => (
  <ul className="zfe-files-ajax-preview-simple">
    {items.map(item => <File {...{ item, ...props }} key={item.key} />)}
  </ul>
);

Preview.propTypes = {
  items: PropTypes.array.isRequired,
};

export default Preview;
