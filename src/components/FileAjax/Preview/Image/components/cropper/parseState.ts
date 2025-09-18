import type { CropperState } from './CropperControls';
import type { Size } from './CropTypes';

export function parseState(
  state: object,
  image: Size
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

    return {
      crop: {
        left: state.x / width,
        top: state.y / height,
        right: (state.x + state.width) / width,
        bottom: (state.y + state.height) / height,
      },
      rotation,
    };
  }

  return undefined;
}

export function formatState({ crop, rotation }: CropperState, image: Size) {
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
    scaleX: 1,
    scaleY: 1,
  };

  console.log(data);

  return data;
}
