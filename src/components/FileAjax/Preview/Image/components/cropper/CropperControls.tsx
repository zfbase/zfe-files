import { useEffect, useMemo, useRef, useState } from 'react';
import { CropperCorners } from './CropperCorners';

import './CropperControls.css';
import type { RectRB, RectWH } from './CropTypes';
import { defaultCrop } from './defaultCrop';

interface CropperControlsProps {
  cropAspectRatio?: number;
  imageAspectRatio: number;
  rotation: number;
  src: string;
}

export const CropperControls: React.FC<CropperControlsProps> = ({
  cropAspectRatio,
  imageAspectRatio,
  rotation,
  src,
}) => {
  const rectRef = useRef<HTMLDivElement>(null);
  const [rect, setRect] = useState<DOMRect>();
  const rotated = rotation % 2 !== 0;

  useEffect(() => {
    function onResize() {
      setRect(rectRef.current?.getBoundingClientRect());
    }
    window.addEventListener('resize', onResize);
    onResize();
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

  const [cr, setCr] = useState<RectRB>(() =>
    defaultCrop(imageAspectRatio, cropAspectRatio)
  );

  const prevRotation = useRef(rotation);
  useEffect(() => {
    const change = rotation - prevRotation.current;
    prevRotation.current = rotation;

    setCr((v) => {
      if (cropAspectRatio) {
        return defaultCrop(
          rotation % 2 === 0 ? imageAspectRatio : 1 / imageAspectRatio,
          cropAspectRatio
        );
      }

      let next = { ...v };
      for (let i = 0; i < change; i++) {
        next = {
          left: 1 - next.bottom,
          top: next.left,
          right: 1 - next.top,
          bottom: next.right,
        };
      }
      for (let i = 0; i > change; i--) {
        next = {
          left: next.top,
          top: 1 - next.right,
          right: next.bottom,
          bottom: 1 - next.left,
        };
      }
      return next;
    });
  }, [cropAspectRatio, imageAspectRatio, rotation]);

  return (
    <div className="zf-cc" ref={rectRef}>
      <div className="zf-cc__image-container">
        <div
          className="zf-cc__image"
          style={{
            backgroundImage: `url(${src})`,
            backgroundSize: pos?.backgroundSize,
            transform: `rotate(${rotation * 90}deg)`,
            transition: 'transform .2s ease, background-size .2s ease',
          }}
        />
      </div>
      {rect && pos && cr && (
        <CropperCorners
          cropAspectRatio={cropAspectRatio}
          rect={rect}
          image={pos.rect}
          onChange={setCr}
          value={cr}
        />
      )}
    </div>
  );
};
