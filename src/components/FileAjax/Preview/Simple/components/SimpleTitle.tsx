interface SimpleTitleProps {
  value: string;
}

export const SimpleTitle: React.FC<SimpleTitleProps> = ({ value }) => {
  return <span>{value}</span>;
};
