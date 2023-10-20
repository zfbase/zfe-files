import { useEffect, useRef, useState } from 'react';
import Trimmer from './Trimmer';

interface VideoPlayerProps {
  src: string;
}

const VideoPlayer: React.FC<VideoPlayerProps> = ({ src }) => {
  const [playing, setPlaying] = useState(false);
  const [trimmer, setTrimmer] = useState(false);
  const playerRef = useRef<HTMLVideoElement>(null);

  const [displayTime, setDisplayTime] = useState(0);

  const [start, setStart] = useState<number | null>(null);
  const [end, setEnd] = useState<number | null>(null);

  useEffect(() => {
    const startInput = document.querySelector<HTMLInputElement>(
      '.form-control[name=timecode_start]',
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
      '.form-control[name=timecode_end]',
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
        onPause={() => setPlaying(false)}
        onPlay={() => setPlaying(true)}
        ref={playerRef}
        src={src}
        onTimeUpdate={(e) => {
          setDisplayTime(e.currentTarget.currentTime);
        }}
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
          <Trimmer
            displayTime={displayTime}
            end={end}
            onEndChange={setEnd}
            onStartChange={setStart}
            playerRef={playerRef}
            playing={playing}
            start={start}
          />
          <input
            name="timecode_start"
            type="hidden"
            value={typeof start === 'number' ? start.toFixed(0) : '0'}
          />
          <input
            name="timecode_end"
            type="hidden"
            value={typeof end === 'number' ? end.toFixed(0) : '0'}
          />
        </>
      )}
    </>
  );
};

export default VideoPlayer;
