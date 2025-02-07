import classNames from 'classnames';
import { ButtonHTMLAttributes, DetailedHTMLProps } from 'react';

type ButtonProps = {
  icon?: string;
  label?: string;
  size?: 'lg' | 'sm' | 'xs';
} & DetailedHTMLProps<
  ButtonHTMLAttributes<HTMLButtonElement>,
  HTMLButtonElement
>;

export const Button: React.FC<ButtonProps> = ({
  className,
  icon,
  label,
  size,
  title,
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
    title={title || label}
    {...props}
  >
    {icon ? <span className={`glyphicon glyphicon-${icon}`} /> : null}
    {icon && label ? ' ' : null}
    {label}
  </button>
);
