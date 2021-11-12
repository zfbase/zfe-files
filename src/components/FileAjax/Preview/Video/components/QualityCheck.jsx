import React from 'react';
import PropTypes from 'prop-types';
import cs from 'classnames';

import { btnSize } from '../../Button';

const QualityCheck = ({ className, size, ...props }) => (
  <button
    type="button"
    className={cs('btn', 'btn-default', btnSize(size), className)}
    title="QualityCheck"
    disabled
    {...props}
  >
    <span className="glyphicon glyphicon-equalizer" />
  </button>
);

QualityCheck.propTypes = {
  className: PropTypes.string,
  size: PropTypes.string,
};

QualityCheck.defaultProps = {
  className: null,
  size: null,
};

export default QualityCheck;
