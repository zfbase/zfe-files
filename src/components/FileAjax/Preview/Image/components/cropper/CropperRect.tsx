import classNames from 'classnames';
import type { CropperPos } from './CropperControls';
import { CropperFade } from './CropperFade';

import './CropperRect.css';
import { useEffect, useMemo, useRef, useState } from 'react';

interface CropperRectProps {
  active?: boolean;
  pos: CropperPos;
  rect: DOMRect;
}

export const CropperRect: React.FC<CropperRectProps> = ({
  active,
  pos,
  rect,
}) => {
  const [activeDelayed, setActiveDelayed] = useState(active);
  const activeRef = useRef<NodeJS.Timeout>(null);

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
            setTimeout(() => toggleDelayed(false));
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
