import { CommonPreviewProps } from '../Preview';
import { Image, ImageItem } from './Image';
import { ImageLoading } from './ImageLoading';

interface ImagePreviewProps extends CommonPreviewProps {
  height: number;
  items: ImageItem[];
  width: number;
}

export const ImagePreview: React.FC<ImagePreviewProps> = ({
  items,
  setData,
  onCancelUpload,
  ...props
}) => (
  <div className="zfe-files-ajax-preview-image-wrap">
    {items.map((item) =>
      item.loading ? (
        <ImageLoading
          height={props.height}
          item={item}
          key={item.key}
          onCancelUpload={onCancelUpload}
          width={props.width}
        />
      ) : (
        <Image
          item={item}
          key={item.key}
          setData={(data) => setData(item.key, data)}
          {...props}
        />
      ),
    )}
  </div>
);
