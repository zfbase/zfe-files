import { ReactEventHandler, useEffect, useMemo, useRef, useState } from 'react';
import { VideoTrimmer } from './VideoTrimmer';

interface VideoPlayerProps {
  src: string;
}

export const VideoPlayer: React.FC<VideoPlayerProps> = ({ src }) => {
  const [playing, setPlaying] = useState(false);
  const [trimmer, setTrimmer] = useState(false);
  const playerRef = useRef<HTMLVideoElement>(null);

  const [displayTime, setDisplayTime] = useState(0);

  const onTimeUpdate = useMemo<ReactEventHandler<HTMLVideoElement>>(
    () => (e) => {
      setDisplayTime(e.currentTarget.currentTime);
    },
    [],
  );

  const [start, setStart] = useState<number | undefined>(undefined);
  const [end, setEnd] = useState<number | undefined>(undefined);

  useEffect(() => {
    const startInput = document.querySelector<HTMLInputElement>(
      'input.form-control[name=timecode_start]',
    );
    if (startInput) {
      const parsedStart = parseFloat(startInput.value);
      if (!Number.isNaN(parsedStart) && parsedStart > 0) {
        setStart(parsedStart);
      }
      startInput.closest('.form-group')?.remove();
      setTrimmer(true);
    }
    const endInput = document.querySelector<HTMLInputElement>(
      'input.form-control[name=timecode_end]',
    );
    if (endInput) {
      const parsedEnd = parseFloat(endInput.value);
      if (!Number.isNaN(parsedEnd) && parsedEnd > 0) {
        setEnd(parsedEnd);
      }
      endInput.closest('.form-group')?.remove();
      setTrimmer(true);
    }
  }, []);

  return (
    <>
      <video
        className="zfe-files-ajax-preview-video-player"
        controls
        onTimeUpdate={onTimeUpdate}
        onPlay={() => setPlaying(true)}
        onPause={() => setPlaying(false)}
        ref={playerRef}
        src={src}
        style={
          trimmer
            ? {
                borderBottomLeftRadius: 0,
                borderBottomRightRadius: 0,
              }
            : undefined
        }
      />
      {trimmer && (
        <>
          <VideoTrimmer
            displayTime={displayTime}
            end={end}
            onEndChange={setEnd}
            onStartChange={setStart}
            playerRef={playerRef}
            playing={playing}
            start={start}
          />
          <input
            type="hidden"
            name="timecode_start"
            value={typeof start === 'number' ? start.toFixed(0) : '0'}
          />
          <input
            type="hidden"
            name="timecode_end"
            value={typeof end === 'number' ? end.toFixed(0) : '0'}
          />
        </>
      )}
    </>
  );
};
