import React, { useRef, useState, useEffect } from 'react';

const usePlayer = (source, props) => {
  const _player = useRef(null);
  const Player = () => <audio ref={_player} src={source} {...props} />;

  const [state, setState] = useState('stop');
  useEffect(() => {
    if (_player) {
      switch (state) {
        case 'play':
          _player.current.play();
          break;
        case 'stop':
          _player.current.currentTime = 0;
        case 'pause':
          _player.current.pause();
          break;
      }
    }
  }, [state]);

  return {
    Player,
    state,
    play: () => setState('play'),
    pause: () => setState('pause'),
    stop: () => setState('stop'),
  };
};

export default usePlayer;
