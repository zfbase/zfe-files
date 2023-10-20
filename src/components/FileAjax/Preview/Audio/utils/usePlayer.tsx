import { useRef, useState, useEffect } from 'react';

function usePlayer(
  source: string,
  props?: React.DetailedHTMLProps<
    React.AudioHTMLAttributes<HTMLAudioElement>,
    HTMLAudioElement
  >,
) {
  const player = useRef<HTMLAudioElement>(null);

  const Player = () => <audio ref={player} src={source} {...(props || {})} />;

  const [state, setState] = useState('stop');
  useEffect(() => {
    if (player.current) {
      switch (state) {
        case 'play':
          player.current.play();
          break;
        case 'stop':
          player.current.currentTime = 0;
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
}

export default usePlayer;
