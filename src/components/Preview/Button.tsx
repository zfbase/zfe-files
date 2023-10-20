import classNames from 'classnames';

export type ButtonSize = 'xs' | 'sm' | 'lg';

export const btnSize = (size: ButtonSize) => (size ? `btn-${size}` : null);

type ButtonProps = {
  icon?: string;
  label?: string;
  size?: ButtonSize;
} & React.DetailedHTMLProps<
  React.ButtonHTMLAttributes<HTMLButtonElement>,
  HTMLButtonElement
>;

export const Button: React.FC<ButtonProps> = ({
  icon,
  label,
  title,
  className,
  size,
  ...props
}) => (
  <button
    title={title || label}
    type="button"
    className={classNames(
      'btn',
      'btn-default',
      size && btnSize(size),
      className,
    )}
    {...props}
  >
    {icon ? <span className={`glyphicon glyphicon-${icon}`} /> : null}
    {icon && label ? ' ' : null}
    {label}
  </button>
);
