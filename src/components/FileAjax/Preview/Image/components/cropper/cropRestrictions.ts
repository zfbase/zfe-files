import {
  isLeft,
  isTop,
  normalizePos,
  type Corner,
  type Pos,
  type RectRB,
  type RectWH,
  type Size,
} from './CropTypes';

export function cropRestrictions(
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

  const opposite: Pos = {
    left: isLeft(corner) ? crop.right : crop.left,
    top: isTop(corner) ? crop.bottom : crop.top,
  };

  let aspect: { angle: number; min: number; max: number } | undefined;
  if (aspectRatio !== undefined) {
    let angle = Math.atan(imageAspectRatio / aspectRatio);
    const min = Math.max(
      minSize.width / Math.cos(angle),
      minSize.height / Math.sin(angle)
    );
    const max = Math.min(
      (isLeft(corner) ? crop.right : 1 - crop.left) / Math.cos(angle),
      (isTop(corner) ? crop.bottom : 1 - crop.top) / Math.sin(angle)
    );

    switch (corner) {
      case 'nw':
        angle -= Math.PI;
        break;
      case 'ne':
        angle = -angle;
        break;
      case 'sw':
        angle = Math.PI - angle;
        break;
    }
    aspect = { angle, min, max };
  }

  function cursorPos(e: Pick<PointerEvent, 'clientX' | 'clientY'>) {
    const pos = normalizePos(
      { left: e.clientX - rect.left, top: e.clientY - rect.top },
      imageRect
    );

    const res: Pos = {
      left: Math.max(allowedRect.left, Math.min(allowedRect.right, pos.left)),
      top: Math.max(allowedRect.top, Math.min(allowedRect.bottom, pos.top)),
    };

    if (aspect !== undefined) {
      const x = res.left - opposite.left;
      const y = res.top - opposite.top;

      const l = Math.min(
        aspect.max,
        Math.max(aspect.min, Math.sqrt(x ** 2 + y ** 2))
      );

      const newX = Math.cos(aspect.angle) * l;
      const newY = Math.sin(aspect.angle) * l;

      return {
        left: opposite.left + newX,
        top: opposite.top + newY,
      };
    }

    return res;
  }

  return { corner, cursorPos };
}

export type CropRestrictions = ReturnType<typeof cropRestrictions>;
