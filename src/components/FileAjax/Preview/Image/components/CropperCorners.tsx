import {
  useEffect,
  useRef,
  useState,
  type Dispatch,
  type SetStateAction,
} from 'react';
import type { CropperPos } from './CropperControls';

const corners = ['nw', 'ne', 'sw', 'se'] as const;
type Corner = (typeof corners)[number];

function rectCoords(pos: CropperPos, image: CropperPos) {
  return {
    top: image.top + pos.top * image.height,
    left: image.left + pos.left * image.width,
    width: pos.width * image.width,
    height: pos.height * image.height,
  };
}

function cornerCoords(
  corner: Corner,
  pos: CropperPos,
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
  onChange: Dispatch<SetStateAction<CropperPos>>;
  rect: DOMRect;
  value: CropperPos;
}

export const CropperCorners: React.FC<CropperCornersProps> = ({
  image,
  onChange,
  rect,
  value,
}) => {
  const [dragging, setDragging] = useState<{ corner: Corner }>();
  const draggingRef = useRef(dragging);
  useEffect(() => {
    draggingRef.current = dragging;
  }, [dragging]);

  useEffect(() => {
    function onPointerMove(e: MouseEvent) {
      if (draggingRef.current) {
        const x = (e.clientX - rect.left - image.left) / image.width;
        const y = (e.clientY - rect.top - image.top) / image.height;
        const corner = draggingRef.current.corner;

        onChange((v) => {
          switch (corner) {
            case 'nw':
              return {
                left: x,
                top: y,
                width: v.width + v.left - x,
                height: v.height + v.top - y,
              };
            case 'ne':
              return {
                left: v.left,
                top: y,
                width: x - v.left,
                height: v.height + v.top - y,
              };
            case 'sw':
              return {
                left: x,
                top: v.top,
                width: v.width + v.left - x,
                height: y - v.top,
              };
            case 'se':
              return {
                left: v.left,
                top: v.top,
                width: x - v.left,
                height: y - v.top,
              };
          }
        });
      }
    }

    function onPointerUp() {
      setDragging(undefined);
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
      <div className="zf-cc__rect" style={rectCoords(value, image)} />

      {corners.map((corner) => (
        <div
          className={`zf-cc__corner zf-cc__corner_${corner}`}
          style={cornerCoords(corner, value, image)}
          onPointerDown={(e) => {
            console.log(e.button);
            if (e.button === 0) {
              e.preventDefault();
              setDragging({ corner });
            }
          }}
        />
      ))}
    </>
  );
};
