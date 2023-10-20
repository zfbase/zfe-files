interface TitleProps {
  label: string;
}

const Title: React.FC<TitleProps> = ({ label }) => (
  <span className="zfe-files-ajax-preview-audio-title">{label}</span>
);

export default Title;
