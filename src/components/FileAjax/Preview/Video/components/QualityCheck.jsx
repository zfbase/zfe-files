import React from 'react';
import PropTypes from 'prop-types';
import cs from 'classnames';

import { btnSize } from '../../Button';

const QualityCheck = ({ className, size, ...props }) => (
  <button
    type="button"
    className={cs('btn', 'btn-default', btnSize(size), className)}
    {...props}
  >
    <span className="glyphicon glyphicon-equalizer" />
  </button>
);

QualityCheck.propTypes = {
  title: PropTypes.string,
  onClick: PropTypes.func,
  className: PropTypes.string,
  size: PropTypes.string,
};

QualityCheck.defaultProps = {
  title: 'QualityCheck',
  // eslint-disable-next-line no-alert
  onClick: () => window.alert('Система контроля качества видео не подключена.'),
  className: null,
  size: null,
};

export default QualityCheck;
