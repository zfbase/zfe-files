/* eslint-disable no-param-reassign */
import React, { useCallback } from 'react';
import PropTypes from 'prop-types';

const formatTime = (t, addSS = false) => {
  if (typeof t === 'number') {
    const h = Math.floor(t / 3600);
    const m = Math.floor((t / 60) % 60);
    const s = Math.floor(t % 60);
    const hms = [h, m, s].map((v) => v.toFixed(0).padStart(2, '0')).join(':');
    return `${hms}${addSS ? `.${t.toFixed(2).split('.')[1]}` : ''}`;
  }
  return '';
};

const roundTimeUp = (t) => Math.ceil(t / 2) * 2;
const roundTimeDown = (t) => Math.floor(t / 2) * 2;

const Trimmer = ({
  displayTime,
  end,
  onEndChange,
  onStartChange,
  playerRef,
  playing,
  start,
}) => {
  const playPause = useCallback(() => {
    if (playerRef.current.paused) {
      playerRef.current.play();
    } else {
      playerRef.current.pause();
    }
  }, [playerRef]);

  const step = useCallback(
    (s) => () => {
      playerRef.current.currentTime = s > 0
        ? roundTimeDown(playerRef.current.currentTime) + s
        : roundTimeUp(playerRef.current.currentTime) + s;
    },
    [playerRef],
  );

  const goStart = useCallback(() => {
    playerRef.current.currentTime = typeof start === 'number' ? start : 0;
  }, [playerRef, start]);

  const goEnd = useCallback(() => {
    playerRef.current.currentTime = typeof end === 'number' ? end : playerRef.current.duration;
  }, [playerRef, end]);

  const saveStart = useCallback(() => {
    let nextStart = roundTimeDown(playerRef.current.currentTime);
    if (typeof end === 'number' && nextStart > end) {
      nextStart = 0;
    }
    onStartChange(nextStart <= 0 ? undefined : nextStart);
  }, [onStartChange, end]);

  const saveEnd = useCallback(() => {
    let nextEnd = roundTimeUp(playerRef.current.currentTime);
    if (typeof start === 'number' && nextEnd < start) {
      nextEnd = Number.Infinity;
    }
    if (nextEnd >= playerRef.current.duration) {
      nextEnd = undefined;
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

Trimmer.propTypes = {
  displayTime: PropTypes.number.isRequired,
  end: PropTypes.number,
  onEndChange: PropTypes.func.isRequired,
  onStartChange: PropTypes.func.isRequired,
  playerRef: PropTypes.shape().isRequired,
  playing: PropTypes.bool,
  start: PropTypes.number,
};

Trimmer.defaultProps = {
  end: undefined,
  playing: false,
  start: undefined,
};

export default Trimmer;
