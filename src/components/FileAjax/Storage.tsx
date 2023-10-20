import { Fragment, ReactNode } from 'react';

interface RenderedItem {
  id: number | string;
  deleted: boolean;
  data?: {};
}

interface LiteRenderProps {
  item: RenderedItem;
  name: string;
}

export const LiteRender: React.FC<LiteRenderProps> = ({ item, name }) => (
  <input type="hidden" name={`${name}[]`} value={item.id} />
);

interface ExtendedRenderProps {
  item: RenderedItem;
  name: string;
}

export const ExtendedRender: React.FC<ExtendedRenderProps> = ({
  item,
  name,
}) => (
  <input
    type="hidden"
    name={`${name}[]`}
    value={JSON.stringify({
      id: item.id,
      ...item.data,
    })}
  />
);

interface StorageProps {
  name: string;
  items: RenderedItem[];
  render?: (item: RenderedItem, name: string) => ReactNode;
}

const Storage: React.FC<StorageProps> = ({ name, items, render }) => {
  const filteredItems = items.filter((item) => item.id && !item.deleted);
  return (
    <Fragment>
      {filteredItems.map((item) =>
        render ? (
          render(item, name)
        ) : item.data ? (
          <ExtendedRender item={item} name={name} key={item.id} />
        ) : (
          <LiteRender item={item} name={name} key={item.id} />
        ),
      )}
      {filteredItems.length === 0 && (
        <input type="hidden" name={`${name}[]`} key="0" />
      )}
    </Fragment>
  );
};

export default Storage;
