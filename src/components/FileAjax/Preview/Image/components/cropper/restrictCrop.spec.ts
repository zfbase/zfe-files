import type { CropperPos2 } from './CropperControls';
import type { Corner } from './CropperCorners';

export function restrictCrop(
  initialPos: CropperPos2,
  corner: Corner,
  imageAspectRatio: number,
  cropAspectRatio?: number
): CropperPos2 {
  const pos = { ...initialPos };

  const minWidth = 0.1;
  const minHeight = minWidth / imageAspectRatio;

  const width = 1 - (pos.left + pos.right);
  const height = 1 - (pos.top + pos.bottom);

  let extraWidth = minWidth - width;
  let extraHeight = minHeight - height;

  if (cropAspectRatio) {
    if (width / height !== cropAspectRatio) {
      const hyp = Math.sqrt(width ** 2 + height ** 2);
      const angle = Math.atan(cropAspectRatio);
      const newWidth = hyp * Math.cos(Math.atan(cropAspectRatio));
      const newHeight = hyp * Math.sin(Math.atan(cropAspectRatio));
      console.log({
        angle: (angle * 180) / Math.PI,
        w: newWidth,
        h: newHeight,
      });

      extraWidth = width - newWidth;
      extraHeight = height - newHeight;
    }
  }

  if (extraWidth > 0) {
    if (corner[1] === 'w') {
      pos.left -= extraWidth;
    } else {
      pos.right -= extraWidth;
    }
  }

  if (extraHeight > 0) {
    if (corner[0] === 'n') {
      pos.top -= extraHeight;
    } else {
      pos.bottom -= extraHeight;
    }
  }

  pos.top = Math.max(0, pos.top);
  pos.bottom = Math.max(0, pos.bottom);
  pos.left = Math.max(0, pos.left);
  pos.right = Math.max(0, pos.right);

  return pos;
}
