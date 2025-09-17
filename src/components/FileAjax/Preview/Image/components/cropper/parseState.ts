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
    return {
      crop: {
        left: state.x / image.width,
        top: state.y / image.height,
        right: (state.x + state.width) / image.width,
        bottom: (state.y + state.height) / image.height,
      },
      rotation: Math.round(state.rotate / 90),
    };
  }

  return undefined;
}
