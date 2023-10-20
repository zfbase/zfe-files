type IconButtonProps = {
  icon: string;
} & React.DetailedHTMLProps<
  React.ButtonHTMLAttributes<HTMLButtonElement>,
  HTMLButtonElement
>;

export const IconButton: React.FC<IconButtonProps> = ({
  className,
  icon,
  ...props
}) => (
  <button
    className={`btn btn-xs btn-default ${className ?? ''}`}
    type="button"
    {...props}
  >
    <span className={`glyphicon glyphicon-${icon}`} />
  </button>
);
