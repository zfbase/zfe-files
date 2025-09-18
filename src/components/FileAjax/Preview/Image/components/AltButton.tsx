import { FaRegComment } from 'react-icons/fa6';
import { Button } from '../../Button';

interface AltData {
  alt?: string;
}

interface AltButtonProps {
  data: AltData;
  disabled?: boolean;
  setData: (data: AltData) => void;
}

export const AltButton: React.FC<AltButtonProps> = ({
  data,
  disabled,
  setData,
}) => (
  <Button
    disabled={disabled}
    label={<FaRegComment />}
    title="Описание для слабовидящих"
    onClick={() => {
      const v = prompt(
        'Описание изображения для версии сайта для слабовидящих',
        data.alt ?? undefined
      );
      if (v !== null) {
        setData({ ...data, alt: v });
      }
    }}
  />
);
