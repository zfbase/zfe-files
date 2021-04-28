import React from 'react';
import PropTypes from 'prop-types';

// Упрощенный classnames что бы не тянуть лишние зависимости
const cs = (...args) => args.reduce((classes, val) => {
  const classNames = [];
  if (typeof val === 'object') {
    Object.entries(val).map(([className, active]) => active && classNames.push(className));
  } else if (typeof val === 'string') {
    classNames.push(val);
  }
  return [...classes, ...classNames];
}, []).join(' ');

const ProgressCircle = ({ percent }) => (
  <div className={cs('zfe-files--progress-circle', { over50: percent > 50 })}>
    {/* eslint-disable-next-line react/jsx-one-expression-per-line */}
    <span>{Math.round(percent)}%</span>
    <div className="zfe-files--progress-circle--left-half-clipper">
      <div className="zfe-files--progress-circle--first50-bar" />
      <div
        className="zfe-files--progress-circle--value-bar"
        style={{ transform: `rotate(${Math.round(3.6 * percent)}deg)` }}
      />
    </div>
  </div>
);

ProgressCircle.propTypes = {
  percent: PropTypes.number,
};

ProgressCircle.defaultProps = {
  percent: 0,
};

export default ProgressCircle;
