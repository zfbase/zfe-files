interface AudioControlBtnProps {
  icon: string;
  onClick: () => unknown;
}

export const AudioControlBtn: React.FC<AudioControlBtnProps> = ({
  icon,
  onClick,
}) => (
  <button type="button" onClick={onClick}>
    <span className={`glyphicon glyphicon-${icon}`} />
  </button>
);
