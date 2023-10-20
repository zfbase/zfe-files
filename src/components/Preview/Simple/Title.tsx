interface TitleProps {
  value: string;
}

export const Title: React.FC<TitleProps> = ({ value }) => <span>{value}</span>;
