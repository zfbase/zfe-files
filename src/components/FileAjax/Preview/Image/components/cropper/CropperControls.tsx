import { useEffect, useMemo, useRef, useState } from 'react';
import { CropperCorners } from './CropperCorners';

import './CropperControls.css';
import type { RectRB, RectWH } from './CropTypes';
import { defaultCrop } from './defaultCrop';

interface CropperControlsProps {
  cropAspectRatio?: number;
  imageAspectRatio: number;
  src: string;
}

export const CropperControls: React.FC<CropperControlsProps> = ({
  cropAspectRatio,
  imageAspectRatio,
  src,
}) => {
  const rectRef = useRef<HTMLDivElement>(null);
  const [rect, setRect] = useState<DOMRect>();

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

  const pos = useMemo<RectWH | undefined>(() => {
    if (!rect) {
      return undefined;
    }

    const ratio = rect.width / rect.height;
    const wide = imageAspectRatio < ratio;

    const width = wide ? rect.height * imageAspectRatio : rect.width;
    const height = wide ? rect.height : rect.width / imageAspectRatio;

    return {
      width: Math.round(width),
      height: Math.round(height),
      left: Math.round((rect.width - width) / 2),
      top: Math.round((rect.height - height) / 2),
    };
  }, [imageAspectRatio, rect]);

  const [cr, setCr] = useState<RectRB>(() =>
    defaultCrop(imageAspectRatio, cropAspectRatio)
  );

  return (
    <div
      className="zf-cc"
      ref={rectRef}
      style={{ backgroundImage: `url(${src})` }}
    >
      {rect && pos && cr && (
        <CropperCorners
          cropAspectRatio={cropAspectRatio}
          rect={rect}
          image={pos}
          onChange={setCr}
          value={cr}
        />
      )}
    </div>
  );
};
