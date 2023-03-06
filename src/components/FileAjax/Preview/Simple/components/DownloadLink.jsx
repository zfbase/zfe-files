import React from 'react';
import PropTypes from 'prop-types';

const DownloadLink = ({ downloadUrl, className }) => (
  <a
    href={downloadUrl}
    target="_blank"
    title="Скачать оригинал"
    className={className}
  >
    <span className="glyphicon glyphicon-download-alt" />
  </a>
);

DownloadLink.propTypes = {
  downloadUrl: PropTypes.string.isRequired,
  className: PropTypes.string,
};

DownloadLink.defaultProps = {
  className: undefined,
};

export default DownloadLink;
