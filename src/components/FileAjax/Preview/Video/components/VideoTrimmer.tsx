import { RefObject, useMemo } from 'react';

function formatTime(t: number | undefined, addSS = false) {
  if (typeof t === 'number') {
    const h = Math.floor(t / 3600);
    const m = Math.floor((t / 60) % 60);
    const s = Math.floor(t % 60);
    const hms = [h, m, s].map((v) => v.toFixed(0).padStart(2, '0')).join(':');
    return `${hms}${addSS ? `.${t.toFixed(2).split('.')[1]}` : ''}`;
  }
  return '';
}

function roundTimeUp(t: number) {
  return Math.ceil(t / 2) * 2;
}

function roundTimeDown(t: number) {
  return Math.floor(t / 2) * 2;
}

interface VideoTrimmerProps {
  displayTime: number;
  end?: number;
  onEndChange: (end: number | undefined) => unknown;
  onStartChange: (start: number | undefined) => unknown;
  playerRef: RefObject<HTMLVideoElement | null>;
  playing?: boolean;
  start?: number;
}

export const VideoTrimmer: React.FC<VideoTrimmerProps> = ({
  displayTime,
  end,
  onEndChange,
  onStartChange,
  playerRef,
  playing,
  start,
}) => {
  const playPause = useMemo(
    () => () => {
      if (playerRef.current) {
        if (playerRef.current.paused) {
          playerRef.current.play();
        } else {
          playerRef.current.pause();
        }
      }
    },
    [playerRef],
  );

  const step = useMemo(
    () => (s: number) => () => {
      if (playerRef.current) {
        playerRef.current.currentTime =
          s > 0
            ? roundTimeDown(playerRef.current.currentTime) + s
            : roundTimeUp(playerRef.current.currentTime) + s;
      }
    },
    [playerRef],
  );

  const goStart = useMemo(
    () => () => {
      if (playerRef.current) {
        playerRef.current.currentTime = typeof start === 'number' ? start : 0;
      }
    },
    [playerRef, start],
  );

  const goEnd = useMemo(
    () => () => {
      if (playerRef.current) {
        playerRef.current.currentTime =
          typeof end === 'number' ? end : playerRef.current.duration;
      }
    },
    [playerRef, end],
  );

  const saveStart = useMemo(
    () => () => {
      if (playerRef.current) {
        let nextStart = roundTimeDown(playerRef.current.currentTime);
        if (typeof end === 'number' && nextStart > end) {
          nextStart = 0;
        }
        onStartChange(nextStart <= 0 ? undefined : nextStart);
      }
    },
    [onStartChange, end],
  );

  const saveEnd = useMemo(
    () => () => {
      if (playerRef.current) {
        let nextEnd: number | undefined = roundTimeUp(
          playerRef.current.currentTime,
        );
        if (typeof start === 'number' && nextEnd < start) {
          nextEnd = Number.POSITIVE_INFINITY;
        }
        if (nextEnd >= playerRef.current.duration) {
          nextEnd = undefined;
        }
        onEndChange(nextEnd);
      }
    },
    [onEndChange, start],
  );

  return (
    <div
      className="d-flex"
      style={{
        marginTop: -5,
        padding: 5,
        border: '1px solid #ddd',
        borderRadius: '0 0 5px 5px',
        borderTopWidth: 0,
        backgroundColor: '#f0f0f0',
        fontFeatureSettings: 'tnum',
        fontVariantNumeric: 'tabular-nums',
      }}
    >
      <div
        style={{
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'space-between',
        }}
      >
        <button
          className="btn btn-default"
          style={{ fontWeight: 900 }}
          onClick={saveStart}
          title="Сохранить позицию начала"
          type="button"
        >
          [
        </button>
        <div
          style={{
            display: 'flex',
            flexDirection: 'row',
            alignItems: 'center',
          }}
        >
          <button className="btn btn-default" onClick={goStart} type="button">
            <span className="glyphicon glyphicon-fast-backward" />
          </button>
          {' '}
          <button className="btn btn-default" onClick={step(-2)} type="button">
            <span className="glyphicon glyphicon-step-backward" />
          </button>
          {' '}
          <button className="btn btn-default" onClick={playPause} type="button">
            <span
              className={`glyphicon glyphicon-${playing ? 'pause' : 'play'}`}
            />
          </button>
          {' '}
          <button className="btn btn-default" onClick={step(2)} type="button">
            <span className="glyphicon glyphicon-step-forward" />
          </button>
          {' '}
          <button className="btn btn-default" onClick={goEnd} type="button">
            <span className="glyphicon glyphicon-fast-forward" />
          </button>
        </div>
        <button
          className="btn btn-default"
          style={{ fontWeight: 900 }}
          onClick={saveEnd}
          title="Сохранить позицию конца"
          type="button"
        >
          ]
        </button>
      </div>

      <div
        style={{
          display: 'flex',
          flexDirection: 'row',
          justifyContent: 'space-between',
          marginTop: 3,
        }}
      >
        <div style={{ width: '30%', textAlign: 'left' }}>
          {formatTime(start, true)}
        </div>
        <div style={{ width: '30%', textAlign: 'center' }}>
          {formatTime(displayTime, true)}
        </div>
        <div style={{ width: '30%', textAlign: 'right' }}>
          {formatTime(end, true)}
        </div>
      </div>
    </div>
  );
};
