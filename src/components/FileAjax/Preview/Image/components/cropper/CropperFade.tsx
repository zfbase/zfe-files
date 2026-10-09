import type { RectRB } from './CropTypes';

type CropperFadeProps = RectRB;

export const CropperFade: React.FC<CropperFadeProps> = ({
  left,
  right,
  top,
  bottom,
}) => {
  return (
    <>
      <div className="zf-cc__fade zf-cc__fade_w" style={{ width: left }} />
      <div className="zf-cc__fade zf-cc__fade_e" style={{ width: right }} />
      <div
        className="zf-cc__fade zf-cc__fade_n"
        style={{ left: left, height: top, right: right }}
      />
      <div
        className="zf-cc__fade zf-cc__fade_s"
        style={{ left, height: bottom, right }}
        title={bottom.toString()}
      />
    </>
  );
};
