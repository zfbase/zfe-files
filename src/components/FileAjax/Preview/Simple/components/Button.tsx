type ButtonProps = {
  icon: string;
} & React.DetailedHTMLProps<
  React.ButtonHTMLAttributes<HTMLButtonElement>,
  HTMLButtonElement
>;

const Button: React.FC<ButtonProps> = ({ className, icon, ...props }) => (
  <button
    className={`btn btn-xs btn-default ${className ?? ''}`}
    type="button"
    {...props}
  >
    <span className={`glyphicon glyphicon-${icon}`} />
  </button>
);

export default Button;
