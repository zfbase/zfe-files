import File, { FileProps, type FileItem } from './File';

type PreviewProps = {
  items: FileItem[];
} & Omit<FileProps, 'item'>;

const Preview: React.FC<PreviewProps> = ({ items, ...props }) => (
  <ul className="zfe-files-ajax-preview-simple">
    {items.map((item) => (
      <File item={item} {...props} key={item.key} />
    ))}
  </ul>
);

export default Preview;
