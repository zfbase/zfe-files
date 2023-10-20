interface ControlBtnProps {
  icon: string;
  onClick: () => void;
}

export const ControlBtn: React.FC<ControlBtnProps> = ({ icon, onClick }) => (
  <button onClick={onClick} type="button">
    <span className={`glyphicon glyphicon-${icon}`} />
  </button>
);
