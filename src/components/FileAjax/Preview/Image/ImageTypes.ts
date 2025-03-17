export interface FileImageData {
  alt?: string;
  scaleX?: number;
  scaleY?: number;
  x?: number;
  y?: number;
  rotate?: number;
}

export interface FileImageItem {
  key: string;
  canvasUrl: string;
  downloadUrl: string;
  previewUrl: string;
  previewLocal: string;
  deleted: boolean;
  data: FileImageData;
  uploadProgress?: number;
  loading?: boolean;
}
