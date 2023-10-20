import Button from './Button';
import DownloadLink from './DownloadLink';
import Icon from './Icon';
import Title from './Title';

export interface FileItem {
  key: string;
  name: string;
  downloadUrl: string;
  deleted: boolean;
  loading: boolean;
  uploadProgress: number;
}

export interface FileProps {
  item: FileItem;
  disabled?: boolean;
  onDelete: (key: string) => void;
  onUndelete: (key: string) => void;
  onCancelUpload: (key: string) => void;
}

function workingMessage(percentage: number) {
  return percentage < 100
    ? `Загрузка… ${percentage ? `${percentage}%` : ''}`
    : 'Обработка…';
}

const File: React.FC<FileProps> = ({
  item,
  disabled,
  onDelete,
  onUndelete,
  onCancelUpload,
}) => (
  <li className={item.deleted ? 'deleted' : undefined}>
    <Icon />
    <Title
      value={
        item.loading
          ? workingMessage(
              item.uploadProgress ? Math.round(item.uploadProgress) : 0,
            )
          : item.name
      }
    />
    {item.downloadUrl && <DownloadLink downloadUrl={item.downloadUrl} />}
    {disabled ? null : item.deleted ? (
      <Button
        icon="repeat"
        title="Восстановить"
        onClick={() => onUndelete(item.key)}
        className="undelete"
      />
    ) : item.loading ? (
      <Button
        icon="remove"
        title="Отменить загрузку"
        onClick={() => onCancelUpload(item.key)}
      />
    ) : (
      <Button
        icon="remove"
        title="Удалить"
        onClick={() => onDelete(item.key)}
      />
    )}
  </li>
);

export default File;
