function twoDigit(num: number) {
  return `0${num}`.slice(-2);
}

export function secToTime(sec?: number) {
  if (typeof sec !== 'number' || Number.isNaN(sec)) {
    return '?';
  }

  const hours = Math.floor(sec / 3600);
  const minutes = Math.floor(sec / 60) - hours * 60;
  const seconds = Math.floor(sec) - hours * 3600 - minutes * 60;

  let timeStr = `${twoDigit(minutes)}:${twoDigit(seconds)}`;
  if (hours > 0) {
    timeStr = `${hours}:${timeStr}`;
  }

  return timeStr;
}

interface AudioTimeProps {
  value?: number;
}

export const AudioTime: React.FC<AudioTimeProps> = ({ value }) => (
  <span>{secToTime(value)}</span>
);
