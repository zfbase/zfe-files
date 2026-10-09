import { useEffect, useState, type Dispatch, type SetStateAction } from 'react';
import { cornerStyle } from './cornerStyle';
import { CropperRect } from './CropperRect';
import { corners, isLeft, isTop, type RectRB, type RectWH } from './CropTypes';
import { cropRestrictions, type CropRestrictions } from './cropRestrictions';

interface CropperCornersProps {
  cropAspectRatio?: number;
  image: RectWH;
  onChange: Dispatch<SetStateAction<RectRB>>;
  rect: DOMRect;
  value: RectRB;
}

export const CropperCorners: React.FC<CropperCornersProps> = ({
  cropAspectRatio,
  image,
  onChange,
  rect,
  value,
}) => {
  const [active, setActive] = useState(false);
  const [drag, setDrag] = useState<CropRestrictions>();

  useEffect(() => {
    if (!drag) {
      return;
    }
    const { corner, cursorPos } = drag;

    function onPointerMove(e: PointerEvent) {
      const pos = cursorPos(e);
      onChange((v) => {
        const next = { ...v };
        if (isTop(corner)) {
          next.top = pos.top;
        } else {
          next.bottom = pos.top;
        }
        if (isLeft(corner)) {
          next.left = pos.left;
        } else {
          next.right = pos.left;
        }
        return next;
      });
    }

    function onPointerUp() {
      setActive(false);
      setDrag(undefined);
    }

    window.addEventListener('pointermove', onPointerMove);
    window.addEventListener('pointerup', onPointerUp);

    return () => {
      window.removeEventListener('pointermove', onPointerMove);
      window.removeEventListener('pointerup', onPointerUp);
    };
  }, [drag, onChange]);

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
          style={cornerStyle(corner, value, image)}
          onPointerDown={(e) => {
            if (e.button === 0) {
              e.preventDefault();
              setActive(true);
              setDrag(
                cropRestrictions(
                  e.nativeEvent,
                  corner,
                  value,
                  image,
                  rect,
                  cropAspectRatio
                )
              );
            }
          }}
        />
      ))}
    </>
  );
};
