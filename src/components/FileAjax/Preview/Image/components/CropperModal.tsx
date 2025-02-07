import { Fragment, useRef, useState } from 'react';
import Cropper, { ReactCropperElement } from 'react-cropper';
import Modal from 'react-modal';
import { Button } from '../../Button';

function filterData<T extends { x: number; y: number }>(data: T): T {
  return {
    ...data,
    x: Math.round(data.x),
    y: Math.round(data.y),
  };
}

interface CropperModalProps {
  src: string;
  width: number | string;
  height: number | string;
  data: object;
  setData: (data: object) => void;
  setPreview: (url: string) => void;
}

export const CropperModal: React.FC<CropperModalProps> = ({
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
  const cropperRef = useRef<ReactCropperElement>(null);

  Modal.setAppElement('body');

  const zoomIn = () => {
    if (cropperRef.current) {
      cropperRef.current.cropper.zoom(0.1);
    }
  };
  const zoomOut = () => {
    if (cropperRef.current) {
      cropperRef.current.cropper.zoom(-0.1);
    }
  };
  const rotateLeft = () => {
    if (cropperRef.current) {
      cropperRef.current.cropper.rotate(-90);
    }
  };
  const rotateRight = () => {
    if (cropperRef.current) {
      cropperRef.current.cropper.rotate(90);
    }
  };
  const flipHorizontal = () => {
    if (cropperRef.current) {
      cropperRef.current.cropper.scaleX(
        -cropperRef.current.cropper.getData().scaleX
      );
    }
  };
  const flipVertical = () => {
    if (cropperRef.current) {
      cropperRef.current.cropper.scaleY(
        -cropperRef.current.cropper.getData().scaleY
      );
    }
  };
  const reset = () => {
    if (cropperRef.current) {
      cropperRef.current.cropper.reset().setData(data);
      closeModal();
    }
  };

  const saveCrop = () => {
    if (cropperRef.current) {
      setData(filterData(cropperRef.current.cropper.getData()));
      setPreview(cropperRef.current.cropper.getCroppedCanvas().toDataURL());
      closeModal();
    }
  };

  return (
    <Fragment>
      <Button
        icon="scissors"
        title="Кадрировать"
        onClick={openModal}
        size="xs"
      />
      <Modal
        isOpen={modalIsOpen}
        onRequestClose={closeModal}
        style={{
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
          overlay: {
            zIndex: 10000,
            cursor: 'pointer',
          },
        }}
      >
        <Cropper
          ref={cropperRef}
          src={src}
          aspectRatio={Number(width) / Number(height)}
          style={{
            overflow: 'hidden',
            textAlign: 'center',
            flexGrow: 1,
          }}
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
