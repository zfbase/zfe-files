import { ButtonHTMLAttributes, DetailedHTMLProps } from 'react';
import { ButtonSize } from '../../../../../CommonTypes';
import classNames from 'classnames';

type VideoQualityCheckProps = {
  size?: ButtonSize;
} & DetailedHTMLProps<
  ButtonHTMLAttributes<HTMLButtonElement>,
  HTMLButtonElement
>;

export const VideoQualityCheck: React.FC<VideoQualityCheckProps> = ({
  className,
  onClick = () =>
    window.alert('Система контроля качества видео не подключена.'),
  size,
  title = 'QualityCheck',
  ...props
}) => (
  <button
    type="button"
    className={classNames(
      'btn',
      'btn-default',
      size ? `btn-${size}` : undefined,
      className,
    )}
    title={title}
    onClick={onClick}
    {...props}
  >
    <span className="glyphicon glyphicon-equalizer" />
  </button>
);
