import React from 'react';

const twodigit = num => `0${num}`.slice(-2);

export const secToTime = (sec) => {
  if (Number.isNaN(sec)) {
    return '?';
  }

  const hours = Math.floor(sec / 3600);
  const minutes = Math.floor(sec / 60) - (hours * 60);
  const seconds = Math.floor(sec) - (hours * 3600) - (minutes * 60);

  let timeStr = `${twodigit(minutes)}:${twodigit(seconds)}`;
  if (hours > 0) {
    timeStr = `${hours}:${timeStr}`;
  }

  return timeStr;
};

const Time = ({ value }) => <span>{secToTime(value)}</span>;

export default Time;