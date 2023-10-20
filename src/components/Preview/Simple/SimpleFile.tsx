import { CommonPreviewProps } from '../Preview';
import { DownloadLink } from './DownloadLink';
import { FileIcon } from './FileIcon';
import { IconButton } from './IconButton';
import { Title } from './Title';

export interface FileItem {
  deleted: boolean;
  downloadUrl: string;
  key: string;
  loading: boolean;
  name: string;
  uploadProgress: number;
}

export interface SimpleFileProps {
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

export const SimpleFile: React.FC<CommonPreviewProps> = ({
  item,
  disabled,
  onDelete,
  onUndelete,
  onCancelUpload,
}) => (
  <li className={item.deleted ? 'deleted' : undefined}>
    <FileIcon />
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
      <IconButton
        className="undelete"
        icon="repeat"
        onClick={() => onUndelete(item.key)}
        title="Восстановить"
      />
    ) : item.loading ? (
      <IconButton
        icon="remove"
        onClick={() => onCancelUpload(item.key)}
        title="Отменить загрузку"
      />
    ) : (
      <IconButton
        icon="remove"
        onClick={() => onDelete(item.key)}
        title="Удалить"
      />
    )}
  </li>
);
