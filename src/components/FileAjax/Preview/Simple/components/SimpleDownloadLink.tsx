interface SimpleDownloadLinkProps {
  downloadUrl: string;
  className?: string;
}

export const SimpleDownloadLink: React.FC<SimpleDownloadLinkProps> = ({
  downloadUrl,
  className,
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
