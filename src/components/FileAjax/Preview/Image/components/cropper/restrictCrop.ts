import type { CropperPos2 } from './CropperControls';
import type { Corner } from './CropperCorners';

export function restrictCrop(
  initialPos: CropperPos2,
  corner: Corner,
  aspectRatio = 1
): CropperPos2 {
  const pos = { ...initialPos };

  const minWidth = 0.1;
  const minHeight = minWidth / aspectRatio;

  const width = 1 - (pos.left + pos.right);
  const height = 1 - (pos.top + pos.bottom);

  const extraWidth = minWidth - width;
  const extraHeight = minHeight - height;

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
