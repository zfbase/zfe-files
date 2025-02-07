import { ReactNode } from 'react';
import { ExtendedRender } from './ExtendedRender';
import { LiteRender } from './LiteRender';
import { StorageItem } from '../../CommonTypes';

interface StorageProps {
  name: string;
  items?: StorageItem[];
  render?: (item: StorageItem, name: string) => ReactNode;
}

export const Storage: React.FC<StorageProps> = ({
  name,
  items = [],
  render = (item, name) =>
    item.data ? (
      <ExtendedRender item={item} name={name} key={item.id} />
    ) : (
      <LiteRender item={item} name={name} key={item.id} />
    ),
}) => {
  const filteredItems = items.filter((item) => item.id && !item.deleted);
  return (
    <>
      {filteredItems.map((item) => render(item, name))}
      {filteredItems.length === 0 && (
        <input type="hidden" name={`${name}[]`} key="0" />
      )}
    </>
  );
};
