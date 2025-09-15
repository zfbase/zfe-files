import type { CropperPos, CropperPos2 } from './CropperControls';

export function rectCoords(pos: CropperPos2, image: CropperPos): CropperPos {
  return {
    top: image.top + pos.top * image.height,
    left: image.left + pos.left * image.width,
    width: image.width * (1 - (pos.right + pos.left)),
    height: image.height * (1 - (pos.bottom + pos.top)),
  };
}
