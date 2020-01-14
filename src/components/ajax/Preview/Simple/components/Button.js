import React from 'react';

const Button = ({ icon, className, ...props }) => (
  <button type="button" className={['btn btn-xs btn-default', className].join(' ')} {...props}>
    <span className={`glyphicon glyphicon-${icon}`} />
  </button>
);

export default Button;
