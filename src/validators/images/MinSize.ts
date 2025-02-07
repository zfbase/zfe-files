export function ValidatorImageMinSize(
  file: File,
  options: { width: number; height: number },
  successCallback: () => unknown,
  failCallback: (message: string) => unknown,
) {
  const { width: minWidth, height: minHeight } = options;

  if (!minWidth || !minHeight) {
    successCallback();
    return;
  }

  const image = new Image();
  const objectUrl = URL.createObjectURL(file);
  image.onload = () => {
    const { width, height } = image;
    URL.revokeObjectURL(objectUrl);
    if (
      (width >= minWidth && height >= minHeight) ||
      (width >= minHeight && height >= minWidth)
    ) {
      successCallback();
    } else {
      failCallback(
        `Изображение должно быть не менее чем ${minWidth}×${minHeight} (загружаемое: ${width}×${height})`,
      );
    }
  };
  image.src = objectUrl;
}
