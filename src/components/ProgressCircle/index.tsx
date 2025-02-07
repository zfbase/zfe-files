import classNames from 'classnames';

interface ProgressCircleProps {
  percent?: number;
}

export const ProgressCircle: React.FC<ProgressCircleProps> = ({
  percent = 0,
}) => (
  <div
    className={classNames('zfe-files--progress-circle', {
      over50: percent > 50,
    })}
  >
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
