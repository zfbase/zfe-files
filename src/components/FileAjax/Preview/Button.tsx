import classNames from 'classnames';
import { ButtonHTMLAttributes, DetailedHTMLProps, type ReactNode } from 'react';
import { ButtonSize, GlyphIconName } from '../../../CommonTypes';

type ButtonProps = {
  icon?: GlyphIconName;
  label?: ReactNode;
  size?: ButtonSize;
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
      className
    )}
    title={title || (typeof label === 'string' ? label : undefined)}
    {...props}
  >
    {icon ? <span className={`glyphicon glyphicon-${icon}`} /> : null}
    {icon && label ? ' ' : null}
    {label}
  </button>
);
