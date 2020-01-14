import React from 'react';
import PropTypes from 'prop-types';

const Button = ({ icon, ...props }) => (
  <button type="button" className="btn btn-xs btn-default" {...props}>
    <span className={`glyphicon glyphicon-${icon}`} />
  </button>
);

Button.propTypes = {
  icon: PropTypes.string.isRequired,
  title: PropTypes.string,
  onClick: PropTypes.func,
};

export default Button;
