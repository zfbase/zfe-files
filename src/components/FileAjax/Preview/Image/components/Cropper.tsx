import classNames from 'classnames';
import { useEffect, useState } from 'react';
import { CropperControls } from './CropperControls';

import './Cropper2.css';

interface CropperProps {
  src: string;
  data: object;
  setData: (data: object) => void;
  setPreview: (url: string) => void;
  onClose: () => void;
}

export const Cropper: React.FC<CropperProps> = ({ onClose, src }) => {
  const [closing, setClosing] = useState(false);
  const [open, setOpen] = useState(false);
  const [stopped, setStopped] = useState(true);
  const [dimensions, setDimensions] = useState<
    { width: number; height: number } | undefined
  >();

  useEffect(() => {
    setOpen(true);

    document.body.style.overflow = 'hidden';

    function onKeyDown(e: KeyboardEvent) {
      if (e.key === 'Escape') {
        setClosing(true);
      }
    }

    window.addEventListener('keydown', onKeyDown);

    return () => {
      window.removeEventListener('keydown', onKeyDown);
      document.body.style.overflow = '';
    };
  }, []);

  return (
    <div
      className={classNames(
        'zf-cropper',
        open && 'zf-cropper_open',
        closing && 'zf-cropper_closing'
      )}
      onTransitionStart={() => setStopped(false)}
      onTransitionEnd={() => {
        if (closing) {
          onClose();
        } else {
          setStopped(true);
        }
      }}
    >
      <div className="zf-cropper__toolbar">
        <button
          className="btn btn-primary ml-auto"
          type="button"
          onClick={() => {
            setClosing(true);
          }}
        >
          Обрезать
        </button>
        <button
          className="btn btn-default"
          type="button"
          onClick={() => {
            setClosing(true);
          }}
        >
          Отмена
        </button>
      </div>

      <img
        src={src}
        alt=""
        onLoad={(e) =>
          setDimensions({
            width: e.currentTarget.naturalWidth,
            height: e.currentTarget.naturalHeight,
          })
        }
      />

      {stopped && dimensions && dimensions.height > 0 && (
        <CropperControls
          imageAspectRatio={dimensions.width / dimensions.height}
          src={src}
        />
      )}
    </div>
  );
};
