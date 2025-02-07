import { useRef, useState, useEffect } from 'react';

export function usePlayer() {
  const ref = useRef<HTMLAudioElement>(null);

  const [state, setState] = useState<'stop' | 'play' | 'pause'>('stop');

  useEffect(() => {
    if (ref.current) {
      switch (state) {
        case 'play':
          ref.current.play();
          break;
        case 'stop':
          ref.current.currentTime = 0;
          ref.current.pause();
          break;
        case 'pause':
          ref.current.pause();
          break;
      }
    }
  }, [state]);

  return {
    ref,
    state,
    play: () => setState('play'),
    pause: () => setState('pause'),
    stop: () => setState('stop'),
  };
}

export default usePlayer;
