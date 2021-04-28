import React from 'react';
import PropTypes from 'prop-types';

const Title = ({ value }) => <span>{value}</span>;

Title.propTypes = {
  value: PropTypes.string.isRequired,
};

export default Title;
