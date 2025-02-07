import { FileItem } from '../../../../../CommonTypes';
import { SimpleFile, SimpleFileProps } from './SimpleFile';

type SimplePreviewProps = {
  items: FileItem[];
} & Omit<SimpleFileProps, 'item'>;

export const SimplePreview: React.FC<SimplePreviewProps> = ({
  items,
  ...rest
}) => {
  return (
    <ul className="zfe-files-ajax-preview-simple">
      {items.map((item) => (
        <SimpleFile item={item} {...rest} key={item.key} />
      ))}
    </ul>
  );
};
