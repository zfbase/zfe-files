import React, {
  createRef,
  Fragment,
  useState,
} from 'react';
import PropTypes from 'prop-types';
import Modal from 'react-modal';
import Cropper from 'react-cropper';

// eslint-disable-next-line import/no-extraneous-dependencies
import 'cropperjs/dist/cropper.css';

import Button from '../../Button';

const filterData = ({
  x,
  y,
  width,
  height,
  rotate,
  // scaleX,
  // scaleY,
}) => ({
  x: Math.round(x),
  y: Math.round(y),
  width,
  height,
  rotate,
  // scaleX,
  // scaleY,
});

const CropperModal = ({
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
  const cropper = createRef(null);

  const defaultViewMode = 1;
  const [viewMode, setViewMode] = useState(defaultViewMode);
  useEffect(() => {
    // if (cropper.current) {
    //   const options = cropper.current.cropper.options;
    //   options.viewMode = viewMode;
    //   cropper.current.cropper.reset().clear().replace(options);
    // }
  }, [viewMode]);

  const styles = {
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

  const zoomIn = () => cropper.current.zoom(0.1);
  const zoomOut = () => cropper.current.zoom(-0.1);
  const rotateLeft = () => cropper.current.rotate(-90);
  const rotateRight = () => cropper.current.rotate(90);
  const flipHorizontal = () => cropper.current.scaleX(-cropper.current.getData().scaleX);
  const flipVertical = () => cropper.current.scaleY(-cropper.current.getData().scaleY);
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
      <Button icon="scissors" title="Кадрировать" onClick={openModal} size="xs" />
      <Modal
        isOpen={modalIsOpen}
        onRequestClose={closeModal}
        style={styles}
      >
        <Cropper
          ref={cropper}
          src={src}
          aspectRatio={width / height}
          style={styles.cropper}
          viewMode={defaultViewMode}
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
            <Button icon="repeat" title="Повернуть" onClick={rotateLeft} style={{ transform: 'scale(-1, 1)' }} />
            <Button icon="repeat" title="Повернуть" onClick={rotateRight} />
          </div>
          <div className="btn-group hide">
            <Button icon="resize-horizontal" title="Отразить по горизонтали" onClick={flipHorizontal} />
            <Button icon="resize-vertical" title="Отразить по вертикали" onClick={flipVertical} />
          </div>
          <Button label="Отменить" onClick={reset} />
          <Button label="Сохранить" onClick={saveCrop} />
        </div>
      </Modal>
    </Fragment>
  );
};

CropperModal.propTypes = {
  src: PropTypes.string.isRequired,
  width: PropTypes.oneOfType([
    PropTypes.number,
    PropTypes.string,
  ]).isRequired,
  height: PropTypes.oneOfType([
    PropTypes.number,
    PropTypes.string,
  ]).isRequired,
  data: PropTypes.shape(),
  setData: PropTypes.func.isRequired,
  setPreview: PropTypes.func.isRequired,
};

CropperModal.defaultProps = {
  data: {},
};

export default CropperModal;
