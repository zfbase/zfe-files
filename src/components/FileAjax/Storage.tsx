import { StorageItem } from '../../CommonTypes';
import { ExtendedRender } from './ExtendedRender';
import { LiteRender } from './LiteRender';

interface StorageProps {
  items?: StorageItem[];
  name: string;
}

export const Storage: React.FC<StorageProps> = ({ name, items = [] }) => {
  const notDeletedItems = items.filter((item) => item.id && !item.deleted);
  return (
    <>
      {notDeletedItems.map((item) =>
        item.data ? (
          <ExtendedRender item={item} name={name} key={item.id} />
        ) : (
          <LiteRender item={item} name={name} key={item.id} />
        )
      )}

      {notDeletedItems.length === 0 && (
        <input type="hidden" name={`${name}[]`} key="0" />
      )}
    </>
  );
};
