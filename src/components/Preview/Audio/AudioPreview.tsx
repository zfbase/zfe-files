import { Audio, type AudioItem, type AudioProps } from './Audio';

type AudioPreviewProps = {
  items: AudioItem[];
} & Omit<AudioProps, 'item'>;

export const AudioPreview: React.FC<AudioPreviewProps> = ({
  items,
  ...props
}) => (
  <ul className="zfe-files-ajax-preview-simple">
    {/* eslint-disable-next-line react/jsx-props-no-spreading */}
    {items.map((item) => (
      <Audio item={item} {...props} Wrapper="li" key={item.key} />
    ))}
  </ul>
);
