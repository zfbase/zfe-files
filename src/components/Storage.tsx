import { Fragment, ReactNode } from 'react';
import { GenericUploadItem } from './Preview/Preview';

interface LiteRenderProps {
  item: GenericUploadItem<unknown>;
  name: string;
}

export const LiteRender: React.FC<LiteRenderProps> = ({ item, name }) => (
  <input name={`${name}[]`} type="hidden" value={item.id} />
);

interface ExtendedRenderProps {
  item: GenericUploadItem<unknown>;
  name: string;
}

export const ExtendedRender: React.FC<ExtendedRenderProps> = ({
  item,
  name,
}) => (
  <input
    name={`${name}[]`}
    type="hidden"
    value={JSON.stringify({
      id: item.id,
      ...(item.data ?? {}),
    })}
  />
);

interface StorageProps {
  items: GenericUploadItem<unknown>[];
  name: string;
  render?: (item: GenericUploadItem<unknown>, name: string) => ReactNode;
}

export const Storage: React.FC<StorageProps> = ({ name, items, render }) => {
  const filteredItems = items.filter((item) => item.id && !item.deleted);

  return (
    <Fragment>
      {filteredItems.map((item) =>
        render ? (
          render(item, name)
        ) : item.data ? (
          <ExtendedRender item={item} key={item.id} name={name} />
        ) : (
          <LiteRender item={item} key={item.id} name={name} />
        ),
      )}
      {filteredItems.length === 0 && (
        <input key="0" name={`${name}[]`} type="hidden" />
      )}
    </Fragment>
  );
};
