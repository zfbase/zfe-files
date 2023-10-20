/* eslint-disable no-alert */
import PropTypes from 'prop-types';
import React from 'react';

import Button from '../../Button';

const AltButton = ({ data, setData }) => (
  <Button
    icon="eye-open"
    title="Кадрировать"
    onClick={() => {
      const v = prompt(
        'Описание изображения для версии сайта для слабовидящих',
        data.alt ?? undefined,
      );
      if (v !== null) {
        setData({ ...data, alt: v });
      }
    }}
    size="xs"
  />
);

AltButton.propTypes = {
  data: PropTypes.shape(),
  setData: PropTypes.func.isRequired,
};

AltButton.defaultProps = {
  data: {},
};

export default AltButton;
