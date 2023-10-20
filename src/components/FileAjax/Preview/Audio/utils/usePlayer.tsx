import React, { useRef, useState, useEffect } from 'react';

const usePlayer = (source, props) => {
  const player = useRef(null);

  // eslint-disable-next-line react/jsx-props-no-spreading
  const Player = () => <audio ref={player} src={source} {...props} />;

  const [state, setState] = useState('stop');
  useEffect(() => {
    if (player) {
      // eslint-disable-next-line default-case
      switch (state) {
        case 'play':
          player.current.play();
          break;
        case 'stop':
          player.current.currentTime = 0;
        // eslint-disable-next-line no-fallthrough
        case 'pause':
          player.current.pause();
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
