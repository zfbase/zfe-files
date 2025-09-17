import type { RectRB } from './CropTypes';

export function defaultCrop(
  imageAspectRatio: number,
  aspectRatio?: number
): RectRB {
  if (aspectRatio === undefined) {
    return { left: 0, top: 0, right: 1, bottom: 1 };
  }

  const angle = Math.atan(imageAspectRatio / aspectRatio);

  const l = Math.min(1 / Math.sin(angle), 1 / Math.cos(angle));

  const w = l * Math.cos(angle);
  const h = l * Math.sin(angle);

  const mx = (1 - w) / 2;
  const my = (1 - h) / 2;

  return { left: mx, right: mx + w, top: my, bottom: my + h };
}
