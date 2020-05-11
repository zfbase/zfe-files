import React from 'react';
import PropTypes from 'prop-types';
import cs from 'classnames';

// eslint-disable-next-line object-curly-newline
const ButtonLink = ({ icon, url, className, ...props }) => (
  <a
    rel="button"
    className={cs('btn btn-xs btn-default', className)}
    href={url}
    target="_blank"
    {...props}
  >
    <span className={`glyphicon glyphicon-${icon}`} />
  </a>
);

ButtonLink.propTypes = {
  icon: PropTypes.string.isRequired,
  url: PropTypes.string.isRequired,
  className: PropTypes.string,
};

ButtonLink.defaultProps = {
  className: null,
};

export default ButtonLink;
