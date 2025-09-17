import {
  isLeft,
  isTop,
  normalizePos,
  type Corner,
  type RectRB,
  type RectWH,
  type Size,
} from './CropTypes';

export function dragRestrictions(
  corner: Corner,
  crop: RectRB,
  imageRect: RectWH,
  rect: RectWH,
  aspectRatio?: number
) {
  const imageAspectRatio = imageRect.width / imageRect.height;

  const minSize: Size = {
    width: imageAspectRatio <= 1 ? 0.1 : 0.1 / imageAspectRatio,
    height: imageAspectRatio > 1 ? 0.1 : 0.1 * imageAspectRatio,
  };

  const allowedRect: RectRB = {
    left: isLeft(corner) ? 0 : crop.left + minSize.width,
    top: isTop(corner) ? 0 : crop.top + minSize.height,
    right: isLeft(corner) ? crop.right - minSize.width : 1,
    bottom: isTop(corner) ? crop.bottom - minSize.height : 1,
  };

  function cursorPos(e: Pick<PointerEvent, 'clientX' | 'clientY'>) {
    const pos = normalizePos(
      { left: e.clientX - rect.left, top: e.clientY - rect.top },
      imageRect
    );

    return {
      left: Math.max(allowedRect.left, Math.min(allowedRect.right, pos.left)),
      top: Math.max(allowedRect.top, Math.min(allowedRect.bottom, pos.top)),
    };
  }

  return { corner, cursorPos };
}

export type DragRestrictions = ReturnType<typeof dragRestrictions>;
