import React from 'react';
import PropTypes from 'prop-types';

const ControlBtn = ({ icon, onClick }) => (
  <button type="button" onClick={onClick}>
    <span className={`glyphicon glyphicon-${icon}`} />
  </button>
);

ControlBtn.propTypes = {
  icon: PropTypes.string.isRequired,
  onClick: PropTypes.func.isRequired,
};

export default ControlBtn;
