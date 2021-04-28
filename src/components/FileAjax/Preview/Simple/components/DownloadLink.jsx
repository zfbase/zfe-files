import React from 'react';
import PropTypes from 'prop-types';

const DownloadLink = ({ downloadUrl }) => (
  <a href={downloadUrl} target="_blank" title="Скачать оригинал">
    <span className="glyphicon glyphicon-download-alt" />
  </a>
);

DownloadLink.propTypes = {
  downloadUrl: PropTypes.string.isRequired,
};

export default DownloadLink;
