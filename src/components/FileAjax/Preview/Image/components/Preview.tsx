import Image, {
  type ImageItem,
  type ImageData,
  type ImageProps,
} from './Image';
import ImageLoading, { type ImageLoadingProps } from './ImageLoading';

type ImagePreviewProps = {
  items: ImageItem[];
  setData: (key: string, data: ImageData) => void;
} & Omit<ImageProps, 'item' | 'setData'> &
  Pick<ImageLoadingProps, 'onCancelUpload'>;

const ImagePreview: React.FC<ImagePreviewProps> = ({
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

export default ImagePreview;
