import Button from '../../Button';
import type { ImageData } from './Image';

interface AltButtonProps {
  data: ImageData;
  setData: (data: ImageData) => void;
}

const AltButton: React.FC<AltButtonProps> = ({ data, setData }) => (
  <Button
    icon="eye-open"
    size="xs"
    title="Кадрировать"
    onClick={() => {
      const v = prompt(
        'Описание изображения для версии сайта для слабовидящих',
        data.alt ?? undefined,
      );
      if (v !== null) {
        setData({ ...data, alt: v });
      }
    }}
  />
);

export default AltButton;
