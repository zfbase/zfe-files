import React from 'react';
import PropTypes from 'prop-types';

const Title = ({ label }) => <span className="zfe-files-ajax-preview-audio-title">{label}</span>;

Title.propTypes = {
  label: PropTypes.string.isRequired,
};

export default Title;
