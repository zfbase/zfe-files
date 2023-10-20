interface ControlBtnProps {
  icon: string;
  onClick: () => void;
}

const ControlBtn: React.FC<ControlBtnProps> = ({ icon, onClick }) => (
  <button type="button" onClick={onClick}>
    <span className={`glyphicon glyphicon-${icon}`} />
  </button>
);

export default ControlBtn;
