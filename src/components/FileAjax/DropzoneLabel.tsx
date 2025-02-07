interface DropzoneLabelProps {
  multiple?: boolean;
}

export const DropzoneLabel: React.FC<DropzoneLabelProps> = ({ multiple }) => (
  <span className="zfe-files-ajax-dropzone-label">
    <span>
      Для загрузки перетащите
      {multiple ? ' файлы ' : ' файл '}в эту область.
    </span>
  </span>
);
