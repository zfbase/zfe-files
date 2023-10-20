import { SimpleFile, type SimpleFileProps, type FileItem } from './SimpleFile';

type PreviewProps = {
  items: FileItem[];
} & Omit<SimpleFileProps, 'item'>;

export const SimplePreview: React.FC<PreviewProps> = ({ items, ...props }) => (
  <ul className="zfe-files-ajax-preview-simple">
    {items.map((item) => (
      <SimpleFile item={item} {...props} key={item.key} />
    ))}
  </ul>
);
