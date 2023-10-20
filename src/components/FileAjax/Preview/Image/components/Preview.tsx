import React from 'react';
import PropTypes from 'prop-types';

import Image from './Image';
import ImageLoading from './ImageLoading';

const Preview = ({ items, setData, ...props }) => (
  <div className="zfe-files-ajax-preview-image-wrap">
    {items.map(item => React.createElement(
      item.loading ? ImageLoading : Image,
      {
        item,
        setData: data => setData(item.key, data),
        ...props,
        key: item.key,
      },
    ))}
  </div>
);

Preview.propTypes = {
  items: PropTypes.arrayOf(PropTypes.shape({
    key: PropTypes.string,
    loading: PropTypes.bool,
  })).isRequired,
  setData: PropTypes.func.isRequired,
};

export default Preview;
