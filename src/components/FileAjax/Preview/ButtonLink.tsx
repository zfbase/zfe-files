import classNames from 'classnames';
import { AnchorHTMLAttributes, DetailedHTMLProps } from 'react';
import { GlyphIconName } from '../../../CommonTypes';

type ButtonLinkProps = {
  icon?: GlyphIconName;
  url?: string;
} & DetailedHTMLProps<
  AnchorHTMLAttributes<HTMLAnchorElement>,
  HTMLAnchorElement
>;

// eslint-disable-next-line object-curly-newline
export const ButtonLink: React.FC<ButtonLinkProps> = ({
  icon,
  url,
  className,
  ...props
}) => (
  <a
    rel="button"
    className={classNames('btn btn-xs btn-default', className)}
    href={url}
    target="_blank"
    {...props}
  >
    <span className={`glyphicon glyphicon-${icon}`} />
  </a>
);
