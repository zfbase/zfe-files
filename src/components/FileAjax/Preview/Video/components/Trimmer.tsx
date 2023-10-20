import { MutableRefObject, useCallback } from 'react';

function formatTime(t: number, addSS = false) {
  const h = Math.floor(t / 3600);
  const m = Math.floor((t / 60) % 60);
  const s = Math.floor(t % 60);
  const hms = [h, m, s].map((v) => v.toFixed(0).padStart(2, '0')).join(':');
  return `${hms}${addSS ? `.${t.toFixed(2).split('.')[1]}` : ''}`;
}

function roundTimeUp(t: number) {
  return Math.ceil(t / 2) * 2;
}

function roundTimeDown(t: number) {
  return Math.floor(t / 2) * 2;
}

interface TrimmerProps {
  displayTime: number;
  end: number | null;
  onEndChange: (value: number | null) => void;
  onStartChange: (value: number | null) => void;
  playerRef: MutableRefObject<HTMLVideoElement | null>;
  playing: boolean;
  start: number | null;
}

const Trimmer: React.FC<TrimmerProps> = ({
  displayTime,
  end,
  onEndChange,
  onStartChange,
  playerRef,
  playing,
  start,
}) => {
  const playPause = useCallback(() => {
    if (!playerRef.current) {
      return;
    }
    if (playerRef.current.paused) {
      playerRef.current.play();
    } else {
      playerRef.current.pause();
    }
  }, [playerRef]);

  const step = useCallback(
    (s: number) => () => {
      if (!playerRef.current) {
        return;
      }
      playerRef.current.currentTime =
        s > 0
          ? roundTimeDown(playerRef.current.currentTime) + s
          : roundTimeUp(playerRef.current.currentTime) + s;
    },
    [playerRef],
  );

  const goStart = useCallback(() => {
    if (playerRef.current) {
      playerRef.current.currentTime = typeof start === 'number' ? start : 0;
    }
  }, [playerRef, start]);

  const goEnd = useCallback(() => {
    if (playerRef.current) {
      playerRef.current.currentTime =
        typeof end === 'number' ? end : playerRef.current.duration;
    }
  }, [playerRef, end]);

  const saveStart = useCallback(() => {
    if (!playerRef.current) {
      return;
    }
    let nextStart = roundTimeDown(playerRef.current.currentTime);
    if (typeof end === 'number' && nextStart > end) {
      nextStart = 0;
    }
    onStartChange(nextStart <= 0 ? null : nextStart);
  }, [onStartChange, end]);

  const saveEnd = useCallback(() => {
    if (!playerRef.current) {
      return;
    }
    let nextEnd: number | null = roundTimeUp(playerRef.current.currentTime);
    if (typeof start === 'number' && nextEnd < start) {
      nextEnd = Number.POSITIVE_INFINITY;
    }
    if (nextEnd >= playerRef.current.duration) {
      nextEnd = null;
    }
    onEndChange(nextEnd);
  }, [onEndChange, start]);

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
          onClick={saveStart}
          style={{ fontWeight: 900 }}
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
          onClick={saveEnd}
          style={{ fontWeight: 900 }}
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
          {start === null ? null : formatTime(start, true)}
        </div>
        <div style={{ width: '30%', textAlign: 'center' }}>
          {formatTime(displayTime, true)}
        </div>
        <div style={{ width: '30%', textAlign: 'right' }}>
          {end === null ? null : formatTime(end, true)}
        </div>
      </div>
    </div>
  );
};

export default Trimmer;
