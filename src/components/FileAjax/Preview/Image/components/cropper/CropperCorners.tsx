import {
  useEffect,
  useRef,
  useState,
  type Dispatch,
  type SetStateAction,
} from 'react';
import { CropperRect } from './CropperRect';
import type { RectRB, RectWH } from './CropTypes';
import { rectCoords } from './rectCoords';
import { restrictCrop } from './restrictCrop';

const corners = ['nw', 'ne', 'sw', 'se'] as const;
export type Corner = (typeof corners)[number];

function cornerCoords(
  corner: Corner,
  pos: RectRB,
  image: RectWH
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
  cropAspectRatio?: number;
  imageAspectRatio: number;
  image: RectWH;
  onChange: Dispatch<SetStateAction<RectRB>>;
  rect: DOMRect;
  value: RectRB;
}

export const CropperCorners: React.FC<CropperCornersProps> = ({
  cropAspectRatio,
  imageAspectRatio,
  image,
  onChange,
  rect,
  value,
}) => {
  const [active, setActive] = useState(false);
  const draggingRef = useRef<Corner>(null);

  useEffect(() => {
    function onPointerMove(e: PointerEvent) {
      if (draggingRef.current) {
        const left = (e.clientX - rect.left - image.left) / image.width;
        const top = (e.clientY - rect.top - image.top) / image.height;
        const right = (rect.right - e.clientX - image.left) / image.width;
        const bottom = (rect.bottom - e.clientY + image.top) / image.height;
        const corner = draggingRef.current;

        onChange((v) => {
          switch (corner) {
            case 'nw':
              return restrictCrop(
                { ...v, left, top },
                corner,
                imageAspectRatio,
                cropAspectRatio
              );
            case 'ne':
              return restrictCrop(
                { ...v, top, right },
                corner,
                imageAspectRatio,
                cropAspectRatio
              );
            case 'sw':
              return restrictCrop(
                { ...v, left, bottom },
                corner,
                imageAspectRatio,
                cropAspectRatio
              );
            case 'se':
              return restrictCrop(
                { ...v, right, bottom },
                corner,
                imageAspectRatio,
                cropAspectRatio
              );
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
  }, [image, imageAspectRatio, onChange, rect]);

  return (
    <>
      <CropperRect
        rect={rect}
        image={image}
        active={active}
        onChange={onChange}
        value={value}
      />

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
