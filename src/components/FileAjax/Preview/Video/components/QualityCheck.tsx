import classNames from 'classnames';
import { btnSize, type ButtonSize } from '../../Button';

type QualityCheckProps = {
  size?: ButtonSize;
} & Omit<
  React.DetailedHTMLProps<
    React.ButtonHTMLAttributes<HTMLButtonElement>,
    HTMLButtonElement
  >,
  'type'
>;

const QualityCheck: React.FC<QualityCheckProps> = ({
  className,
  size,
  title,
  onClick,
  ...props
}) => (
  <button
    title={title ?? 'QualityCheck'}
    type="button"
    className={classNames(
      'btn',
      'btn-default',
      size && btnSize(size),
      className,
    )}
    onClick={
      onClick ?? (() => alert('Система контроля качества видео не подключена.'))
    }
    {...props}
  >
    <span className="glyphicon glyphicon-equalizer" />
  </button>
);

export default QualityCheck;
