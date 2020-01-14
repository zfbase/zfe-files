import React from 'react';

import File from './File';

const Preview = ({ items, ...props }) => (
  <ul className="zfe-files-ajax-preview-simple">
    {items.map(item => <File {...{ item, ...props }} key={item.key} />)}
  </ul>
);

export default Preview;
