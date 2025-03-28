import { Button } from '../../Button';
import { ProgressCircle } from '../../../../ProgressCircle/index';
import { FileImageItem } from '../ImageTypes';

const loadingMock =
  'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iMTcxIiBoZWlnaHQ9IjE4MCIgdmlld0JveD0iMCAwIDE3MSAxODAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjwhLS0KU291cmNlIFVSTDogaG9sZGVyLmpzLzEwMCV4MTgwCkNyZWF0ZWQgd2l0aCBIb2xkZXIuanMgMi42LjAuCkxlYXJuIG1vcmUgYXQgaHR0cDovL2hvbGRlcmpzLmNvbQooYykgMjAxMi0yMDE1IEl2YW4gTWFsb3BpbnNreSAtIGh0dHA6Ly9pbXNreS5jbwotLT48ZGVmcz48c3R5bGUgdHlwZT0idGV4dC9jc3MiPjwhW0NEQVRBWyNob2xkZXJfMTZmYWRhMDg4OWUgdGV4dCB7IGZpbGw6I0FBQUFBQTtmb250LXdlaWdodDpib2xkO2ZvbnQtZmFtaWx5OkFyaWFsLCBIZWx2ZXRpY2EsIE9wZW4gU2Fucywgc2Fucy1zZXJpZiwgbW9ub3NwYWNlO2ZvbnQtc2l6ZToxMHB0IH0gXV0+PC9zdHlsZT48L2RlZnM+PGcgaWQ9ImhvbGRlcl8xNmZhZGEwODg5ZSI+PHJlY3Qgd2lkdGg9IjE3MSIgaGVpZ2h0PSIxODAiIGZpbGw9IiNFRUVFRUUiLz48Zz48dGV4dCB4PSI1OS41NDY4NzUiIHk9Ijk0LjUiPjE3MXgxODA8L3RleHQ+PC9nPjwvZz48L3N2Zz4=';

export interface ImageLoadingProps {
  item: FileImageItem;
  width?: number | string;
  height?: number | string;
  onCancelUpload: (key: string) => void;
}

export const ImageLoading: React.FC<ImageLoadingProps> = ({
  item,
  width,
  height,
  onCancelUpload,
}) => (
  <div className="zfe-files-ajax-preview-image thumbnail">
    <div className="btn-toolbar" role="toolbar">
      <Button
        icon="remove"
        title="Отменить"
        onClick={() => onCancelUpload(item.key)}
      />
    </div>
    <ProgressCircle percent={item.uploadProgress} />
    <div
      className="zfe-files-ajax-preview-image-canvas"
      style={{
        backgroundImage: `url(${item.previewLocal || loadingMock})`,
        width: `${width || 200}px`,
        height: `${height || 200}px`,
      }}
    />
  </div>
);
