import classNames from 'classnames';

type ButtonLinkProps = {
  icon: string;
  url: string;
} & React.DetailedHTMLProps<
  React.AnchorHTMLAttributes<HTMLAnchorElement>,
  HTMLAnchorElement
>;

const ButtonLink: React.FC<ButtonLinkProps> = ({
  className,
  icon,
  url,
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

export default ButtonLink;
