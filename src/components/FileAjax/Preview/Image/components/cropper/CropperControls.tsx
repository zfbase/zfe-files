import { useEffect, useMemo, useRef, useState } from 'react';
import { CropperCorners } from './CropperCorners';

import './CropperControls.css';

export interface CropperPos {
  left: number;
  top: number;
  width: number;
  height: number;
}

export interface CropperPos2 {
  left: number;
  top: number;
  right: number;
  bottom: number;
}

interface CropperControlsProps {
  imageAspectRatio: number;
  src: string;
}

export const CropperControls: React.FC<CropperControlsProps> = ({
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

  const pos = useMemo<CropperPos | undefined>(() => {
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

  const [cr, setCr] = useState<CropperPos2>({
    left: 0,
    top: 0,
    bottom: 0,
    right: 0,
  });

  return (
    <div
      className="zf-cc"
      ref={rectRef}
      style={{ backgroundImage: `url(${src})` }}
    >
      {rect && pos && cr && (
        <CropperCorners rect={rect} image={pos} onChange={setCr} value={cr} />
      )}
    </div>
  );
};
