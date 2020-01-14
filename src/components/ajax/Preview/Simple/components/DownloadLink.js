import React from 'react';

const DownloadLink = ({ downloadUrl }) => (
  <a href={downloadUrl} target="_blank" title="Скачать оригинал">
    <span className="glyphicon glyphicon-download-alt" />
  </a>
);

export default DownloadLink;
