import classNames from 'classnames';
import { AnchorHTMLAttributes, DetailedHTMLProps, type ReactNode } from 'react';
import { GlyphIconName } from '../../../CommonTypes';

type ButtonLinkProps = {
  disabled?: boolean;
  icon?: GlyphIconName;
  label?: ReactNode;
  url?: string;
} & DetailedHTMLProps<
  AnchorHTMLAttributes<HTMLAnchorElement>,
  HTMLAnchorElement
>;

export const ButtonLink: React.FC<ButtonLinkProps> = ({
  disabled,
  icon,
  label,
  url,
  className,
  ...props
}) => (
  <a
    rel="button"
    className={classNames('btn btn-default', className, { disabled })}
    href={url}
    target="_blank"
    {...props}
  >
    {icon && <span className={`glyphicon glyphicon-${icon}`} />}
    {label}
  </a>
);
