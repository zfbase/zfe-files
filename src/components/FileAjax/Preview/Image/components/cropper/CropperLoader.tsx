import { useEffect, useState } from 'react';
import { Cropper } from './Cropper';
import './Cropper.css';
import type { Size } from './CropTypes';
import classNames from 'classnames';

interface CropperLoaderProps {
  aspectRatio?: number;
  data: object;
  onClose: () => void;
  setData: (data: object) => void;
  setPreview: (url: string) => void;
  src: string;
}

export const CropperLoader: React.FC<CropperLoaderProps> = (props) => {
  const [closing, setClosing] = useState(false);
  const [open, setOpen] = useState(false);
  const [stopped, setStopped] = useState(true);

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

  const [imageSize, setImageSize] = useState<Size | undefined>();

  return (
    <div
      className={classNames(
        'zf-cropper',
        open && 'zf-cropper_open',
        closing && 'zf-cropper_closing'
      )}
      onTransitionStart={(e) => {
        if (e.target !== e.currentTarget) {
          return;
        }
        setStopped(false);
      }}
      onTransitionEnd={(e) => {
        if (e.target !== e.currentTarget) {
          return;
        }
        if (closing) {
          props.onClose();
        } else {
          setStopped(true);
        }
      }}
    >
      <img
        className="zf-cropper__image_hidden"
        src={props.src}
        alt=""
        onLoad={(e) =>
          setImageSize({
            width: e.currentTarget.naturalWidth,
            height: e.currentTarget.naturalHeight,
          })
        }
      />

      {stopped && imageSize && (
        <Cropper
          imageSize={imageSize}
          {...props}
          onClose={() => setClosing(true)}
        />
      )}
    </div>
  );
};
