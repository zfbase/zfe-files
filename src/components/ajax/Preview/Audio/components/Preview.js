import React from "react";

import Audio from './Audio';

const Preview = ({ items, ...props }) => (
  <ul className="zfe-files-ajax-preview-simple">
    {items.map(item => <Audio {...{ item, ...props }} Wrapper="li" key={item.key} />)}
  </ul>
);

export default Preview;
