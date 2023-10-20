import { AudioPreview } from './Audio/AudioPreview';
import { ImageData, ImageItem } from './Image/Image';
import { ImagePreview } from './Image/ImagePreview';
import { SimplePreview } from './Simple/SimplePreview';
import { VideoItem } from './Video/Video';
import { VideoPreview } from './Video/VideoPreview';

export interface GenericUploadItem<D = void> {
  data: D;
  deleted: boolean;
  downloadUrl?: string;
  id?: number;
  key: string;
  loading: boolean;
  name: string;
  previewUrl?: string;
  size: string;
  uploadProgress: number;
}

interface GenericPreviewCommon<D = void> {
  disabled?: boolean;
  onCancelUpload: (key: string) => void;
  onDelete: (key: string) => void;
  onUndelete: (key: string) => void;
  setData: (data: D) => void;
}

export interface GenericPreviewItem<T extends GenericUploadItem<D>, D = void>
  extends GenericPreviewCommon<D> {
  item: T[];
}

export interface GenericPreviewItems<T extends GenericUploadItem<D>, D = void> {
  items: T[];
}

export interface PreviewPropsImage
  extends GenericPreviewItems<ImageItem, ImageData> {
  PreviewComponent?: React.FC<GenericPreviewItems<ImageItem, ImageData>>;
  height: number;
  type: 'image';
  width: number;
}

export interface PreviewPropsVideo extends GenericPreviewItems<VideoItem> {
  PreviewComponent?: React.FC<GenericPreviewItems<VideoItem>>;
  type: 'video';
}

export interface PreviewPropsAudio extends GenericPreviewItems<VideoItem> {
  PreviewComponent?: React.FC<GenericPreviewItems<VideoItem>>;
  type: 'audio';
}

export interface PreviewPropsSimple
  extends GenericPreviewItems<GenericUploadItem> {
  PreviewComponent?: React.FC<GenericPreviewItems<GenericUploadItem>>;
  type: '';
}

type PreviewProps =
  | PreviewPropsImage
  | PreviewPropsVideo
  | PreviewPropsAudio
  | PreviewPropsSimple;

// interface PreviewProps {
//   PreviewComponent?: React.FC<CommonPreviewProps<unknown>>;
//   type?: string;
// }

export const Preview: React.FC<PreviewProps> = ({
  PreviewComponent,
  type,
  ...props
}) => {
  if (PreviewComponent) {
    return <PreviewComponent {...props} />;
  }
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
};
