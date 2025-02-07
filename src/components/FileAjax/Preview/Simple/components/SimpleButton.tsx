import { ButtonHTMLAttributes, DetailedHTMLProps } from 'react';
import { GlyphIconName } from '../../../../../CommonTypes';

type SimpleButtonProps = { icon: GlyphIconName } & DetailedHTMLProps<
  ButtonHTMLAttributes<HTMLButtonElement>,
  HTMLButtonElement
>;

export const SimpleButton: React.FC<SimpleButtonProps> = ({
  icon,
  className,
  ...props
}) => (
  <button
    type="button"
    className={['btn btn-xs btn-default', className].join(' ')}
    {...props}
  >
    <span className={`glyphicon glyphicon-${icon}`} />
  </button>
);
