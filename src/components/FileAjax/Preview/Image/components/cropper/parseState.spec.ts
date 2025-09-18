import { formatState, parseState } from './parseState';

test('parseState', () => {
  expect(
    parseState(
      {
        x: 200,
        y: 100,
        width: 800,
        height: 300,
        rotate: 180,
      },
      {
        width: 1000,
        height: 500,
      }
    )
  ).toEqual({
    crop: { left: 0.2, top: 0.2, bottom: 0.8, right: 1 },
    rotation: 2,
  });
});

test('formatState', () => {
  expect(
    formatState(
      {
        crop: { left: 0.2, top: 0.2, bottom: 0.8, right: 1 },
        rotation: 1,
      },
      {
        width: 1000,
        height: 500,
      }
    )
  ).toEqual({
    x: 100,
    y: 200,
    width: 400,
    height: 600,
    rotate: 90,
    scaleX: 1,
    scaleY: 1,
  });
});
