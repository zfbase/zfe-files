interface ControlBtnProps {
  icon: string;
  onClick: () => void;
}

const ControlBtn: React.FC<ControlBtnProps> = ({ icon, onClick }) => (
  <button onClick={onClick} type="button">
    <span className={`glyphicon glyphicon-${icon}`} />
  </button>
);

export default ControlBtn;
