import { useEffect, useRef, useState } from 'react';
import { CropperControls, CropperState } from './CropperControls';

import './Cropper.css';
import { rotateRB, type Size } from './CropTypes';
import { defaultCrop } from './defaultCrop';
import { formatState, parseState } from './parseState';
import { FaRotateLeft, FaRotateRight } from 'react-icons/fa6';

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
  setData,
  src,
}) => {
  const imageAspectRatio = imageSize.width / imageSize.height;

  const [state, setState] = useState<CropperState>(() => ({
    crop: defaultCrop(imageAspectRatio, aspectRatio),
    rotation: 0,
  }));

  const dataRef = useRef(data);
  useEffect(() => {
    dataRef.current = data;
  }, [data]);

  useEffect(() => {
    setState(
      parseState(dataRef.current, imageSize) ?? {
        crop: defaultCrop(imageAspectRatio, aspectRatio),
        rotation: 0,
      }
    );
  }, [aspectRatio, imageSize, imageAspectRatio]);

  return (
    <>
      <div className="zf-cropper__toolbar">
        <div className="btn-toolbar" role="toolbar">
          <div className="btn-group" role="group">
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
              <FaRotateLeft />
            </button>

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
              <FaRotateRight />
            </button>
          </div>
        </div>

        <button
          className="btn btn-primary ml-auto"
          type="button"
          onClick={() => {
            setData({ ...data, ...formatState(state, imageSize) });
            onClose();
          }}
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
