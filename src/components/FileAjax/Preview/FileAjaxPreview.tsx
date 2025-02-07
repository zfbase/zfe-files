import { ReactNode } from 'react';
import { AudioPreview } from './Audio/components/AudioPreview';
import { ImagePreview } from './Image/components/ImagePreview';
import { SimplePreview } from './Simple/components/SimplePreview';
import { VideoPreview } from './Video/components/VideoPreview';
import { FileItem } from '../../../CommonTypes';

interface FileAjaxPreviewProps {
  disabled?: boolean;
  items: FileItem[];
  previewRender?: () => ReactNode;
  type?: 'image' | 'audio' | 'video';
  onDelete: (key: string) => unknown;
  onUndelete: (key: string) => unknown;
  onCancelUpload: (key: string) => unknown;
  setData: (key: string, data: FileItem['data']) => unknown;
}

export const FileAjaxPreview: React.FC<FileAjaxPreviewProps> = ({
  previewRender,
  type,
  ...props
}) => {
  let Helper;
  if (typeof previewRender === 'function') {
    Helper = previewRender;
  } else {
    switch (type) {
      case 'image':
        return <ImagePreview {...props} />;
      case 'audio':
        return <AudioPreview {...props} />;
      case 'video':
        return <VideoPreview {...props} />;
      default:
        return <SimplePreview {...props} />;
    }
  }
};
