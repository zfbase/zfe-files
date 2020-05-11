import React from 'react';
import PropTypes from 'prop-types';

const twoDigit = num => `0${num}`.slice(-2);

export const secToTime = (sec) => {
  if (Number.isNaN(sec)) {
    return '?';
  }

  const hours = Math.floor(sec / 3600);
  const minutes = Math.floor(sec / 60) - (hours * 60);
  const seconds = Math.floor(sec) - (hours * 3600) - (minutes * 60);

  let timeStr = `${twoDigit(minutes)}:${twoDigit(seconds)}`;
  if (hours > 0) {
    timeStr = `${hours}:${timeStr}`;
  }

  return timeStr;
};

const Time = ({ value }) => <span>{secToTime(value)}</span>;

Time.propTypes = {
  value: PropTypes.number,
};

Time.defaultProps = {
  value: null,
};

export default Time;