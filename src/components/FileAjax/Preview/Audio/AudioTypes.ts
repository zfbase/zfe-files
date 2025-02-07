export interface AudioFileItem {
  key: string;
  name: string;
  duration: number;
  downloadUrl: string;
  previewUrl: string;
  deleted: boolean;
  loading?: boolean;
}
