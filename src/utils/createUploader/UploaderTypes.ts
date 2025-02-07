export interface SimpleUploaderProps {
  url: string;
  file: File;
  params?: Record<string, string>;
  onStart?: () => void;
  onProgress?: (progress: { loaded: number; total: number }) => void;
  onComplete?: (file: {}) => void;
  onError?: (err: unknown) => void;
}

export interface ChunksUploaderProps extends SimpleUploaderProps {
  chunkSize?: number;
  maxChunkSize?: number;
  maxThreads?: number;
}

export abstract class Uploader {
  protected url;
  protected file;
  protected params;
  protected onStart;
  protected onComplete;
  protected onError;

  constructor(props: SimpleUploaderProps) {
    this.url = props.url;
    this.file = props.file;
    this.params = props.params ?? {};
    this.onStart = props.onStart ?? (() => {});
    this.onComplete = props.onComplete ?? (() => {});
    this.onError = props.onError ?? (() => {});
  }

  start() {}

  abort() {}

  continue() {}
}
