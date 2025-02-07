import { FileItem } from '../../../../../CommonTypes';
import { FileIcon } from './FileIcon';
import { SimpleButton } from './SimpleButton';
import { SimpleDownloadLink } from './SimpleDownloadLink';
import { SimpleTitle } from './SimpleTitle';

export interface SimpleFileProps {
  item: FileItem;
  disabled?: boolean;
  onDelete: (key: string) => unknown;
  onUndelete: (key: string) => unknown;
  onCancelUpload: (key: string) => unknown;
}

function workingMessage(percentage: number) {
  return percentage < 100
    ? `Загрузка… ${percentage ? `${percentage}%` : ''}`
    : 'Обработка…';
}

export const SimpleFile: React.FC<SimpleFileProps> = ({
  item,
  disabled,
  onDelete,
  onUndelete,
  onCancelUpload,
}) => (
  <li className={item.deleted ? 'deleted' : undefined}>
    <FileIcon />
    <SimpleTitle
      value={
        item.loading && typeof item.uploadProgress === 'number'
          ? workingMessage(Math.round(item.uploadProgress))
          : item.name
      }
    />
    {item.downloadUrl && <SimpleDownloadLink downloadUrl={item.downloadUrl} />}
    {disabled ? null : item.deleted ? ( // eslint-disable-line no-nested-ternary
      <SimpleButton
        icon="repeat"
        title="Восстановить"
        onClick={() => onUndelete(item.key)}
        className="undelete"
      />
    ) : item.loading ? (
      <SimpleButton
        icon="remove"
        title="Отменить загрузку"
        onClick={() => onCancelUpload(item.key)}
      />
    ) : (
      <SimpleButton
        icon="remove"
        title="Удалить"
        onClick={() => onDelete(item.key)}
      />
    )}
  </li>
);
