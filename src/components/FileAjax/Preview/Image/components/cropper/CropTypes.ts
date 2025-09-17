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

interface Size {
  width: number;
  height: number;
}

const SquareOne: Size = {
  width: 1,
  height: 1,
};

export function toRectRB(rect: RectWH, container = SquareOne): RectRB {
  return {
    left: rect.left,
    top: rect.top,
    right: container.width - (rect.left + rect.width),
    bottom: container.height - (rect.top + rect.height),
  };
}

export function toRectWH(rect: RectRB, container = SquareOne): RectWH {
  return {
    left: rect.left,
    top: rect.top,
    width: container.width - (rect.left + rect.right),
    height: container.height - (rect.top + rect.bottom),
  };
}
