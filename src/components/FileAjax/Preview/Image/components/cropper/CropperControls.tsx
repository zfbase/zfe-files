import {
  useEffect,
  useMemo,
  useRef,
  useState,
  type Dispatch,
  type SetStateAction,
} from 'react';
import { CropperCorners } from './CropperCorners';
import type { RectRB, RectWH } from './CropTypes';

import './CropperControls.css';

export interface CropperState {
  crop: RectRB;
  rotation: number;
}

interface CropperControlsProps {
  cropAspectRatio?: number;
  imageAspectRatio: number;
  onChange: Dispatch<SetStateAction<CropperState>>;
  src: string;
  value: CropperState;
}

export const CropperControls: React.FC<CropperControlsProps> = ({
  cropAspectRatio,
  imageAspectRatio,
  onChange,
  src,
  value,
}) => {
  const rectRef = useRef<HTMLDivElement>(null);
  const [rect, setRect] = useState<DOMRect>();
  const rotated = value.rotation % 2 !== 0;

  const [loaded, setLoaded] = useState(false);
  useEffect(() => {
    function onResize() {
      setRect(rectRef.current?.getBoundingClientRect());
    }
    window.addEventListener('resize', onResize);
    onResize();
    setTimeout(() => setLoaded(true), 100);
    return () => {
      window.removeEventListener('resize', onResize);
    };
  }, []);

  const pos = useMemo<
    { rect: RectWH; backgroundSize: string | undefined } | undefined
  >(() => {
    if (!rect) {
      return undefined;
    }

    const iar = rotated ? 1 / imageAspectRatio : imageAspectRatio;

    const containerAspectRatio = rect.width / rect.height;
    const wide = iar < containerAspectRatio;

    const width = wide ? rect.height * iar : rect.width;
    const height = wide ? rect.height : rect.width / iar;

    let backgroundSize: string | undefined = undefined;
    if (rotated) {
      backgroundSize = `${height}px ${width}px`;
    } else {
      backgroundSize = `${width}px ${height}px`;
    }

    return {
      rect: {
        width: width,
        height: height,
        left: (rect.width - width) / 2,
        top: (rect.height - height) / 2,
      },
      backgroundSize,
    };
  }, [imageAspectRatio, rect, rotated]);

  return (
    <div className="zf-cc" ref={rectRef}>
      <div className="zf-cc__image-container">
        <div
          className="zf-cc__image"
          style={{
            backgroundImage: `url(${src})`,
            backgroundSize: pos?.backgroundSize,
            transform: `rotate(${value.rotation * 90}deg)`,
            transition: loaded ? 'transform .2s ease' : undefined,
          }}
        />
      </div>
      {rect && pos && (
        <CropperCorners
          cropAspectRatio={cropAspectRatio}
          rect={rect}
          image={pos.rect}
          onChange={(crop) =>
            onChange((v) => {
              const next = typeof crop === 'function' ? crop(v.crop) : crop;
              return { ...v, crop: next };
            })
          }
          value={value.crop}
        />
      )}
    </div>
  );
};
