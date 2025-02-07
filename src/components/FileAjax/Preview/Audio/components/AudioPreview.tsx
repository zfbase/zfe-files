import { AudioFileItem } from '../AudioTypes';
import { Audio, AudioProps } from './Audio';

type AudioPreviewProps = {
  items: AudioFileItem[];
} & Omit<AudioProps, 'item' | 'Wrapper'>;

export const AudioPreview: React.FC<AudioPreviewProps> = ({
  items,
  ...props
}) => (
  <ul className="zfe-files-ajax-preview-simple">
    {items.map((item) => (
      <Audio item={item} {...props} Wrapper="li" key={item.key} />
    ))}
  </ul>
);
