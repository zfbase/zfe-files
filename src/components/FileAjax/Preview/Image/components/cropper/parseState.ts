import type { CropperState } from './CropperControls';
import type { Pos, Size } from './CropTypes';

export function parseState(
  state: object,
  image: Size,
  hasAspectRatio: boolean
): CropperState | undefined {
  if (
    'x' in state &&
    'y' in state &&
    'width' in state &&
    'height' in state &&
    'rotate' in state &&
    typeof state.x === 'number' &&
    typeof state.y === 'number' &&
    typeof state.width === 'number' &&
    typeof state.height === 'number' &&
    typeof state.rotate === 'number'
  ) {
    const rotation = Math.round(state.rotate / 90);
    const rotated = rotation % 2 === 1;

    const width = rotated ? image.height : image.width;
    const height = rotated ? image.width : image.height;

    const gravity: Pos = { left: 0.5, top: 0.5 };

    if (!hasAspectRatio) {
      if (
        'scaleX' in state &&
        'scaleY' in state &&
        typeof state.scaleX === 'number' &&
        typeof state.scaleY === 'number'
      ) {
        gravity.left = state.scaleX / 100;
        gravity.top = state.scaleY / 100;
      }
    }

    return {
      crop: {
        left: state.x / width,
        top: state.y / height,
        right: (state.x + state.width) / width,
        bottom: (state.y + state.height) / height,
      },
      gravity,
      rotation,
    };
  }

  return undefined;
}

export function formatState(
  { crop, gravity, rotation }: CropperState,
  image: Size,
  hasAspectRatio: boolean
) {
  let r = rotation;
  if (r < 0) {
    r -= Math.floor(r / 4) * 4;
  }

  const rotated = r % 2 === 1;
  const width = rotated ? image.height : image.width;
  const height = rotated ? image.width : image.height;

  const data = {
    x: Math.round(crop.left * width),
    y: Math.round(crop.top * height),
    width: Math.round((crop.right - crop.left) * width),
    height: Math.round((crop.bottom - crop.top) * height),
    rotate: (r % 4) * 90,
    scaleX: hasAspectRatio ? 1 : Math.round(gravity.left * 100),
    scaleY: hasAspectRatio ? 1 : Math.round(gravity.top * 100),
  };

  return data;
}
