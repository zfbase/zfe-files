interface TitleProps {
  value: string;
}

const Title: React.FC<TitleProps> = ({ value }) => <span>{value}</span>;

export default Title;
