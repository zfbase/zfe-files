import React from 'react';
import PropTypes from 'prop-types';
import cs from 'classnames';

const btnSize = (size) => (size ? `btn-${size}` : null);

const Button = ({
  icon,
  label,
  title,
  className,
  size,
  ...props
}) => (
  <button
    type="button"
    className={cs('btn', 'btn-default', btnSize(size), className)}
    title={title || label}
    {...props}
  >
    {icon ? <span className={`glyphicon glyphicon-${icon}`} /> : null}
    {icon && label ? ' ' : null}
    {label}
  </button>
);

Button.propTypes = {
  icon: PropTypes.string,
  label: PropTypes.string,
  title: PropTypes.string,
  onClick: PropTypes.func.isRequired,
  className: PropTypes.string,
  size: PropTypes.string,
};

Button.defaultProps = {
  icon: null,
  label: null,
  title: '',
  className: null,
  size: null,
};

export default Button;
