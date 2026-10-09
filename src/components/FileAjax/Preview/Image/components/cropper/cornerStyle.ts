import type { CSSProperties } from 'react';
import {
  denormalizeRectRB,
  type Corner,
  type RectRB,
  type RectWH,
} from './CropTypes';

export function cornerStyle(
  corner: Corner,
  pos: RectRB,
  image: RectWH
): Partial<CSSProperties> {
  const { top, left, width, height } = denormalizeRectRB(pos, image);
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
