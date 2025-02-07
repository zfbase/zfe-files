import { Button } from '../../Button';

interface AltData {
  alt?: string;
}

interface AltButtonProps {
  data: AltData;
  setData: (data: AltData) => void;
}

export const AltButton: React.FC<AltButtonProps> = ({ data, setData }) => (
  <Button
    icon="eye-open"
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
    size="xs"
  />
);
