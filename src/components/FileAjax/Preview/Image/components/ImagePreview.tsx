import { FileImageItem } from '../ImageTypes';
import { Image, ImageProps } from './Image';
import { ImageLoading, ImageLoadingProps } from './ImageLoading';

type ImagePreviewProps = {
  items: FileImageItem[];
} & Omit<ImageProps & ImageLoadingProps, 'item'>;

export const ImagePreview: React.FC<ImagePreviewProps> = ({
  items,
  ...rest
}) => (
  <div className="zfe-files-ajax-preview-image-wrap">
    {items.map((item) =>
      item.loading ? (
        <ImageLoading key={item.key} item={item} {...rest} />
      ) : (
        <Image key={item.key} item={item} {...rest} />
      )
    )}
  </div>
);
