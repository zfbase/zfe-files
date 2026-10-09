import { useEffect, useState, type Dispatch, type SetStateAction } from 'react';
import type { Pos, RectWH } from './CropTypes';

function centerPos(
  e: PointerEvent,
  click: Pos,
  start: Pos,
  image: RectWH
): Pos {
  const left = (e.clientX - click.left) / image.width + start.left;
  const top = (e.clientY - click.top) / image.height + start.top;
  return {
    left: Math.min(1, Math.max(0, left)),
    top: Math.min(1, Math.max(0, top)),
  };
}

interface CropCenterProps {
  image: RectWH;
  onChange: Dispatch<SetStateAction<Pos>>;
  value: Pos;
}

export const CropCenter: React.FC<CropCenterProps> = ({
  image,
  onChange,
  value,
}) => {
  const [drag, setDrag] = useState<{ click: Pos; start: Pos }>();

  const [dragPos, setDragPos] = useState<Pos>();

  useEffect(() => {
    if (!drag) {
      return;
    }
    const { click, start } = drag;

    function onPointerMove(e: PointerEvent) {
      e.preventDefault();
      setDragPos(centerPos(e, click, start, image));
    }

    function onPointerUp(e: PointerEvent) {
      e.preventDefault();
      setDragPos(undefined);
      setDrag(undefined);
      onChange(centerPos(e, click, start, image));
    }

    window.addEventListener('pointermove', onPointerMove);
    window.addEventListener('pointerup', onPointerUp);

    return () => {
      window.removeEventListener('pointermove', onPointerMove);
      window.removeEventListener('pointerup', onPointerUp);
    };
  }, [drag, image, onChange]);

  const left = image.left + image.width * (dragPos ?? value).left;
  const top = image.top + image.height * (dragPos ?? value).top;

  return (
    <div
      className="zf-cropper__center"
      onMouseDown={(e) => {
        if (e.button === 0) {
          e.preventDefault();
          setDrag({ click: { left: e.clientX, top: e.clientY }, start: value });
        }
      }}
      style={{ left, top }}
      onDoubleClick={() => onChange({ left: 0.5, top: 0.5 })}
    >
      <div className="zf-cropper__center__crosshair_n" />
      <div className="zf-cropper__center__crosshair_e" />
      <div className="zf-cropper__center__crosshair_s" />
      <div className="zf-cropper__center__crosshair_w" />
    </div>
  );
};
