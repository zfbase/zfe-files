import React from 'react';
import PropTypes from 'prop-types';

const Button = ({ icon, className, ...props }) => (
  <button type="button" className={['btn btn-xs btn-default', className].join(' ')} {...props}>
    <span className={`glyphicon glyphicon-${icon}`} />
  </button>
);

Button.propTypes = {
  icon: PropTypes.string.isRequired,
  className: PropTypes.string,
};

Button.defaultProps = {
  className: null,
};

export default Button;
