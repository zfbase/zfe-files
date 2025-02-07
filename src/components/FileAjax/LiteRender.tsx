import { StorageItem } from '../../CommonTypes';

interface LiteRenderProps {
  item: StorageItem;
  name: string;
}

export const LiteRender: React.FC<LiteRenderProps> = ({ item, name }) => (
  <input type="hidden" name={`${name}[]`} value={item.id} />
);
