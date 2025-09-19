export interface RectWH {
  left: number;
  top: number;
  width: number;
  height: number;
}

export interface RectRB {
  left: number;
  top: number;
  right: number;
  bottom: number;
}

export interface Pos {
  left: number;
  top: number;
}

export interface Size {
  width: number;
  height: number;
}

export function toRectRB(rect: RectWH): RectRB {
  return {
    left: rect.left,
    top: rect.top,
    right: rect.left + rect.width,
    bottom: rect.top + rect.height,
  };
}

export function toRectWH(rect: RectRB): RectWH {
  return {
    left: rect.left,
    top: rect.top,
    width: rect.right - rect.left,
    height: rect.bottom - rect.top,
  };
}

export const corners = ['nw', 'ne', 'sw', 'se'] as const;
export type Corner = (typeof corners)[number];

export function isLeft(corner: Corner): corner is 'nw' | 'sw' {
  return corner[1] === 'w';
}

export function isTop(corner: Corner): corner is 'nw' | 'ne' {
  return corner[0] === 'n';
}

export function normalizePos(pos: Pos, origin: RectWH): Pos {
  return {
    left: (pos.left - origin.left) / origin.width,
    top: (pos.top - origin.top) / origin.height,
  };
}

export function denormalizePos(pos: Pos, origin: RectWH): Pos {
  return {
    left: pos.left * origin.width + origin.left,
    top: pos.top * origin.height + origin.top,
  };
}

export function denormalizeRectWH(rect: RectWH, origin: RectWH): RectWH {
  return {
    left: rect.left * origin.width + origin.left,
    top: rect.top * origin.height + origin.top,
    width: rect.width * origin.width,
    height: rect.height * origin.height,
  };
}

export function denormalizeRectRB(rect: RectRB, origin: RectWH): RectWH {
  return denormalizeRectWH(toRectWH(rect), origin);
}

export function rotateRB(rect: RectRB, cw: boolean): RectRB {
  return cw
    ? {
        left: 1 - rect.bottom,
        top: rect.left,
        right: 1 - rect.top,
        bottom: rect.right,
      }
    : {
        left: rect.top,
        top: 1 - rect.right,
        right: rect.bottom,
        bottom: 1 - rect.left,
      };
}

export function rotatePos(rect: Pos, cw: boolean): Pos {
  return cw
    ? {
        left: 1 - rect.top,
        top: rect.left,
      }
    : {
        left: rect.top,
        top: 1 - rect.left,
      };
}
