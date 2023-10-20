import { CSSProperties, Fragment, useRef, useState } from 'react';
import Modal from 'react-modal';
import Cropper, { ReactCropperElement } from 'react-cropper';

// eslint-disable-next-line import/no-extraneous-dependencies
import 'cropperjs/dist/cropper.css';

import Button from '../../Button';
import { ImageData } from './Image';

function filterData({
  x,
  y,
  width,
  height,
  rotate,
}: // scaleX,
// scaleY,
any) {
  return {
    x: Math.round(x),
    y: Math.round(y),
    width,
    height,
    rotate,
    // scaleX,
    // scaleY,
  };
}

interface CropperModalProps {
  src: string;
  width: number;
  height: number;
  data: ImageData;
  setData: (data: ImageData) => void;
  setPreview: (preview: string) => void;
}

const CropperModal: React.FC<CropperModalProps> = ({
  src,
  width,
  height,
  data,
  setData,
  setPreview,
}) => {
  const [modalIsOpen, setIsOpen] = useState(false);
  const openModal = () => setIsOpen(true);
  const closeModal = () => setIsOpen(false);
  const cropper = useRef<HTMLImageElement | ReactCropperElement>(null);

  const styles: Record<'overlay' | 'content' | 'cropper', CSSProperties> = {
    overlay: {
      zIndex: 10000,
      cursor: 'pointer',
    },
    content: {
      top: '5%',
      left: '5%',
      bottom: '5%',
      right: '5%',
      display: 'flex',
      flexDirection: 'column',
      alignItems: 'stretch',
      cursor: 'auto',
    },
    cropper: {
      overflow: 'hidden',
      textAlign: 'center',
      flexGrow: 1,
    },
  };

  Modal.setAppElement('body');

  const zoomIn = () => {
    if (cropper.current) {
      cropper.current.zoom(0.1);
    }
  };
  const zoomOut = () => cropper.current.zoom(-0.1);
  const rotateLeft = () => cropper.current.rotate(-90);
  const rotateRight = () => cropper.current.rotate(90);
  const flipHorizontal = () =>
    cropper.current.scaleX(-cropper.current.getData().scaleX);
  const flipVertical = () =>
    cropper.current.scaleY(-cropper.current.getData().scaleY);
  const reset = () => {
    cropper.current.reset().setData(data);
    closeModal();
  };

  const saveCrop = () => {
    setData(filterData(cropper.current.getData()));
    setPreview(cropper.current.getCroppedCanvas().toDataURL());
    closeModal();
  };

  return (
    <Fragment>
      <Button
        icon="scissors"
        title="Кадрировать"
        onClick={openModal}
        size="xs"
      />
      <Modal isOpen={modalIsOpen} onRequestClose={closeModal} style={styles}>
        <Cropper
          ref={cropper}
          src={src}
          aspectRatio={width / height}
          style={styles.cropper}
          viewMode={1}
          data={data}
          rotatable
          checkOrientation={false}
        />
        <div className="cropper-toolbar form-inline btn-toolbar" role="toolbar">
          <div className="btn-group">
            <Button icon="zoom-in" title="Увеличить" onClick={zoomIn} />
            <Button icon="zoom-out" title="Уменьшить" onClick={zoomOut} />
          </div>
          <div className="btn-group">
            <Button
              icon="repeat"
              title="Повернуть"
              onClick={rotateLeft}
              style={{ transform: 'scale(-1, 1)' }}
            />
            <Button icon="repeat" title="Повернуть" onClick={rotateRight} />
          </div>
          <div className="btn-group hide">
            <Button
              icon="resize-horizontal"
              title="Отразить по горизонтали"
              onClick={flipHorizontal}
            />
            <Button
              icon="resize-vertical"
              title="Отразить по вертикали"
              onClick={flipVertical}
            />
          </div>
          <Button label="Отменить" onClick={reset} />
          <Button label="Сохранить" onClick={saveCrop} />
        </div>
      </Modal>
    </Fragment>
  );
};

export default CropperModal;
