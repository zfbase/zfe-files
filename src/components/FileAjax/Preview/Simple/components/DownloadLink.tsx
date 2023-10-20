interface DownloadLinkProps {
  className?: string;
  downloadUrl: string;
}

const DownloadLink: React.FC<DownloadLinkProps> = ({
  className,
  downloadUrl,
}) => (
  <a
    href={downloadUrl}
    target="_blank"
    title="Скачать оригинал"
    className={className}
  >
    <span className="glyphicon glyphicon-download-alt" />
  </a>
);

export default DownloadLink;
