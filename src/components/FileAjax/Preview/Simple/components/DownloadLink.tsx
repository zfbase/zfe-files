interface DownloadLinkProps {
  className?: string;
  downloadUrl: string;
}

const DownloadLink: React.FC<DownloadLinkProps> = ({
  className,
  downloadUrl,
}) => (
  <a
    className={className}
    href={downloadUrl}
    rel="noreferrer"
    target="_blank" title="Скачать оригинал"
  >
    <span className="glyphicon glyphicon-download-alt" />
  </a>
);

export default DownloadLink;
