import React from 'react';
import PropTypes from 'prop-types';
import cs from 'classnames';

const Button = ({ icon, className, ...props }) => (
  <button type="button" className={cs('btn btn-xs btn-default', className)} {...props}>
    <span className={`glyphicon glyphicon-${icon}`} />
  </button>
);

Button.propTypes = {
  icon: PropTypes.string.isRequired,
  // title: PropTypes.string,
  onClick: PropTypes.func.isRequired,
  className: PropTypes.string,
};

Button.defaultProps = {
  className: null,
};

export default Button;
