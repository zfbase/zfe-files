import { StorageItem } from '../../CommonTypes';

interface ExtendedRenderProps {
  item: StorageItem;
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
