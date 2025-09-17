import { useEffect, useState } from 'react';
import { CropperControls, CropperState } from './CropperControls';

import './Cropper.css';
import { rotateRB, type Size } from './CropTypes';
import { defaultCrop } from './defaultCrop';
import { parseState } from './parseState';

interface CropperProps {
  aspectRatio?: number;
  data: object;
  imageSize: Size;
  onClose: () => void;
  setData: (data: object) => void;
  setPreview: (url: string) => void;
  src: string;
}

export const Cropper: React.FC<CropperProps> = ({
  aspectRatio,
  data,
  imageSize,
  onClose,
  src,
}) => {
  const imageAspectRatio = imageSize.width / imageSize.height;

  const [state, setState] = useState<CropperState>(() => ({
    crop: defaultCrop(imageAspectRatio, aspectRatio),
    rotation: 0,
  }));

  useEffect(() => {
    setState(
      parseState(data, imageSize) ?? {
        crop: defaultCrop(imageAspectRatio, aspectRatio),
        rotation: 0,
      }
    );
  }, [aspectRatio, data, imageSize, imageAspectRatio]);

  return (
    <>
      <div className="zf-cropper__toolbar">
        <button
          className="btn btn-default"
          type="button"
          onClick={() =>
            setState((v) => ({
              crop:
                aspectRatio === undefined
                  ? rotateRB(v.crop, true)
                  : defaultCrop(
                      v.rotation % 2 === 0
                        ? 1 / imageAspectRatio
                        : imageAspectRatio,
                      aspectRatio
                    ),
              rotation: v.rotation + 1,
            }))
          }
        >
          <span className="glyphicon glyphicon-repeat" />
        </button>

        <button
          className="btn btn-default"
          type="button"
          onClick={() =>
            setState((v) => ({
              crop:
                aspectRatio === undefined
                  ? rotateRB(v.crop, false)
                  : defaultCrop(
                      v.rotation % 2 === 0
                        ? 1 / imageAspectRatio
                        : imageAspectRatio,
                      aspectRatio
                    ),
              rotation: v.rotation - 1,
            }))
          }
        >
          <span
            className="glyphicon glyphicon-repeat"
            style={{ transform: 'scale(-1,1)' }}
          />
        </button>

        <button
          className="btn btn-primary ml-auto"
          type="button"
          onClick={onClose}
        >
          Обрезать
        </button>
        <button className="btn btn-default" type="button" onClick={onClose}>
          Отмена
        </button>
      </div>

      <CropperControls
        cropAspectRatio={aspectRatio}
        imageAspectRatio={imageAspectRatio}
        onChange={setState}
        src={src}
        value={state}
      />
    </>
  );
};
