interface AudioTitleProps {
  label: string;
}

export const AudioTitle: React.FC<AudioTitleProps> = ({ label }) => (
  <span className="zfe-files-ajax-preview-audio-title">{label}</span>
);
