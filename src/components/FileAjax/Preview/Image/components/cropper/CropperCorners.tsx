import {
  useEffect,
  useRef,
  useState,
  type Dispatch,
  type SetStateAction,
} from 'react';
import type { CropperPos, CropperPos2 } from './CropperControls';
import { CropperRect } from './CropperRect';
import { restrictCrop } from './restrictCrop';

const corners = ['nw', 'ne', 'sw', 'se'] as const;
export type Corner = (typeof corners)[number];

function rectCoords(pos: CropperPos2, image: CropperPos): CropperPos {
  return {
    top: image.top + pos.top * image.height,
    left: image.left + pos.left * image.width,
    width: image.width * (1 - (pos.right + pos.left)),
    height: image.height * (1 - (pos.bottom + pos.top)),
  };
}

function cornerCoords(
  corner: Corner,
  pos: CropperPos2,
  image: CropperPos
): { left: number; top: number } {
  const { top, left, width, height } = rectCoords(pos, image);
  const right = left + width;
  const bottom = top + height;
  switch (corner) {
    case 'nw':
      return { top: top - 2, left: left - 2 };
    case 'ne':
      return { top: top - 2, left: right - 20 };
    case 'sw':
      return { top: bottom - 20, left: left - 2 };
    case 'se':
      return { top: bottom - 20, left: right - 20 };
  }
}

interface CropperCornersProps {
  image: CropperPos;
  onChange: Dispatch<SetStateAction<CropperPos2>>;
  rect: DOMRect;
  value: CropperPos2;
}

export const CropperCorners: React.FC<CropperCornersProps> = ({
  image,
  onChange,
  rect,
  value,
}) => {
  const [active, setActive] = useState(false);
  const draggingRef = useRef<Corner>(null);

  useEffect(() => {
    function onPointerMove(e: MouseEvent) {
      if (draggingRef.current) {
        const left = (e.clientX - rect.left - image.left) / image.width;
        const top = (e.clientY - rect.top - image.top) / image.height;
        const right = (rect.right - e.clientX - image.left) / image.width;
        const bottom = (rect.bottom - e.clientY + image.top) / image.height;
        const corner = draggingRef.current;

        onChange((v) => {
          switch (corner) {
            case 'nw':
              return restrictCrop({ ...v, left, top }, corner);
            case 'ne':
              return restrictCrop({ ...v, top, right }, corner);
            case 'sw':
              return restrictCrop({ ...v, left, bottom }, corner);
            case 'se':
              return restrictCrop({ ...v, right, bottom }, corner);
          }
        });
      }
    }

    function onPointerUp() {
      draggingRef.current = null;
      setActive(false);
    }

    window.addEventListener('pointermove', onPointerMove);
    window.addEventListener('pointerup', onPointerUp);

    return () => {
      window.removeEventListener('pointermove', onPointerMove);
      window.removeEventListener('pointerup', onPointerUp);
    };
  }, [image, onChange, rect]);

  return (
    <>
      <CropperRect pos={rectCoords(value, image)} rect={rect} active={active} />

      {corners.map((corner) => (
        <div
          className={`zf-cc__corner zf-cc__corner_${corner}`}
          key={corner}
          style={cornerCoords(corner, value, image)}
          onPointerDown={(e) => {
            if (e.button === 0) {
              e.preventDefault();
              draggingRef.current = corner;
              setActive(true);
            }
          }}
        />
      ))}
    </>
  );
};
