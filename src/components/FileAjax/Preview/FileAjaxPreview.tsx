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
  if (previewRender) {
    const Helper = previewRender;
    return <Helper {...(props as any)} />;
  }
  switch (type) {
    case 'image':
      return <ImagePreview {...(props as any)} />;
    case 'audio':
      return <AudioPreview {...(props as any)} />;
    case 'video':
      return <VideoPreview {...(props as any)} />;
    default:
      return <SimplePreview {...props} />;
  }
};
