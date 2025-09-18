import { useEffect, useRef, useState } from 'react';
import { Cropper } from './Cropper';
import './Cropper.css';
import type { Size } from './CropTypes';
import classNames from 'classnames';
import { FaRotateLeft, FaRotateRight } from 'react-icons/fa6';

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
  const closingRef = useRef(false);
  const [open, setOpen] = useState(false);
  const [stopped, setStopped] = useState(true);
  const container = useRef<HTMLDivElement>(null);
  const { onClose } = props;

  useEffect(() => {
    const div = container.current;
    if (!div) {
      return;
    }

    function onTransitionStart(e: TransitionEvent) {
      if (e.target !== e.currentTarget) {
        return;
      }
      setStopped(false);
    }
    function onTransitionEnd(e: TransitionEvent) {
      if (e.target !== e.currentTarget) {
        return;
      }
      if (closing) {
        onClose();
      } else {
        setStopped(true);
      }
    }
    div.addEventListener('transitionstart', onTransitionStart);
    div.addEventListener('transitionend', onTransitionEnd);
    return () => {
      div.removeEventListener('transitionstart', onTransitionStart);
      div.removeEventListener('transitionend', onTransitionEnd);
    };
  }, [closing, onClose]);

  useEffect(() => {
    setOpen(true);

    document.body.style.overflow = 'hidden';

    function onKeyDown(e: KeyboardEvent) {
      if (e.key === 'Escape') {
        closingRef.current = true;
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
      ref={container}
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

      {stopped && imageSize ? (
        <Cropper
          imageSize={imageSize}
          {...props}
          onClose={() => {
            closingRef.current = true;
            setClosing(true);
          }}
        />
      ) : (
        <div className="zf-cropper__toolbar">
          <div className="btn-toolbar" role="toolbar">
            <div className="btn-group" role="group">
              <button className="btn btn-default" type="button" disabled>
                <FaRotateLeft />
              </button>
              <button className="btn btn-default" type="button" disabled>
                <FaRotateRight />
              </button>
            </div>
          </div>

          <button className="btn btn-primary ml-auto" type="button" disabled>
            Обрезать
          </button>

          <button
            className="btn btn-default"
            type="button"
            onClick={props.onClose}
          >
            Отмена
          </button>
        </div>
      )}
    </div>
  );
};
