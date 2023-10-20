import Button from './Button';
import DownloadLink from './DownloadLink';
import Icon from './Icon';
import Title from './Title';

export interface FileItem {
  deleted: boolean;
  downloadUrl: string;
  key: string;
  loading: boolean;
  name: string;
  uploadProgress: number;
}

export interface FileProps {
  disabled?: boolean;
  item: FileItem;
  onCancelUpload: (key: string) => void;
  onDelete: (key: string) => void;
  onUndelete: (key: string) => void;
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
        className="undelete"
        icon="repeat"
        onClick={() => onUndelete(item.key)}
        title="Восстановить"
      />
    ) : item.loading ? (
      <Button
        icon="remove"
        onClick={() => onCancelUpload(item.key)}
        title="Отменить загрузку"
      />
    ) : (
      <Button
        icon="remove"
        onClick={() => onDelete(item.key)}
        title="Удалить"
      />
    )}
  </li>
);

export default File;
