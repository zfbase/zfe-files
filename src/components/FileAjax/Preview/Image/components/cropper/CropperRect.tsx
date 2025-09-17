import classNames from 'classnames';
import { CropperFade } from './CropperFade';

import {
  useEffect,
  useMemo,
  useRef,
  useState,
  type Dispatch,
  type SetStateAction,
} from 'react';
import './CropperRect.css';
import { denormalizeRectRB, type RectRB, type RectWH } from './CropTypes';

interface CropperRectProps {
  active?: boolean;
  image: RectWH;
  onChange: Dispatch<SetStateAction<RectRB>>;
  rect: DOMRect;
  value: RectRB;
}

export const CropperRect: React.FC<CropperRectProps> = ({
  active,
  image,
  onChange,
  rect,
  value,
}) => {
  const [activeDelayed, setActiveDelayed] = useState(active);
  const activeRef = useRef<NodeJS.Timeout>(null);
  const [dragging, setDragging] = useState<{
    x: number;
    y: number;
    value: RectRB;
  }>();
  const pos = denormalizeRectRB(value, image);

  const toggleDelayed = useMemo(
    () => (value: boolean) => {
      if (activeRef.current) {
        clearTimeout(activeRef.current);
      }
      if (value) {
        setActiveDelayed(true);
      } else {
        activeRef.current = setTimeout(() => setActiveDelayed(false), 2000);
      }
    },
    []
  );

  useEffect(() => {
    toggleDelayed(!!active);
  }, [active, toggleDelayed]);

  useEffect(() => {
    if (!dragging) {
      return;
    }

    function onPointerMove(e: PointerEvent) {
      if (!dragging) {
        return;
      }

      let x = (e.clientX - dragging.x) / image.width;
      let y = (e.clientY - dragging.y) / image.height;

      const next = { ...dragging.value };

      if (x > 0) {
        x = Math.min(x, 1 - next.right);
      } else {
        x = Math.max(x, -next.left);
      }
      next.left += x;
      next.right += x;

      if (y > 0) {
        y = Math.min(y, 1 - next.bottom);
      } else {
        y = Math.max(y, -next.top);
      }

      next.top += y;
      next.bottom += y;

      onChange(next);
    }

    function onPointerUp() {
      setDragging(undefined);
      toggleDelayed(false);
    }

    window.addEventListener('pointermove', onPointerMove);
    window.addEventListener('pointerup', onPointerUp);

    return () => {
      window.removeEventListener('pointermove', onPointerMove);
      window.removeEventListener('pointerup', onPointerUp);
    };
  }, [dragging, image, onChange, toggleDelayed]);

  return (
    <>
      <CropperFade
        left={pos.left}
        top={pos.top}
        right={Math.max(0, rect.width - (pos.width + pos.left))}
        bottom={Math.max(0, rect.height - (pos.height + pos.top))}
      />

      <div
        className={classNames(
          'zf-cc__rect',
          activeDelayed && 'zf-cc__rect_active'
        )}
        style={pos}
        onPointerDown={(e) => {
          if (e.button === 0) {
            toggleDelayed(true);
            setDragging({ x: e.clientX, y: e.clientY, value });
          }
        }}
      >
        <div className="zf-cc__third zf-cc__third_n" />
        <div className="zf-cc__third zf-cc__third_s" />
        <div className="zf-cc__third zf-cc__third_e" />
        <div className="zf-cc__third zf-cc__third_w" />
      </div>
    </>
  );
};
