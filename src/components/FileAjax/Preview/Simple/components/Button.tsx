type ButtonProps = {
  icon: string;
} & React.DetailedHTMLProps<
  React.ButtonHTMLAttributes<HTMLButtonElement>,
  HTMLButtonElement
>;

const Button: React.FC<ButtonProps> = ({ className, icon, ...props }) => (
  <button
    type="button"
    className={`btn btn-xs btn-default ${className ?? ''}`}
    {...props}
  >
    <span className={`glyphicon glyphicon-${icon}`} />
  </button>
);

export default Button;
