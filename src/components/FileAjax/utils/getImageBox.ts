// Расчет области для кадрирования
export function getImageBox({
  proxyHeight,
  proxyWidth,
  imageHeight,
  imageWidth,
}: {
  proxyHeight: number;
  proxyWidth: number;
  imageHeight: number;
  imageWidth: number;
}) {
  const xk = imageWidth / proxyWidth;
  const yk = imageHeight / proxyHeight;
  const width = xk <= yk ? imageWidth : Math.round(proxyWidth * yk);
  const height = yk <= xk ? imageHeight : Math.round(proxyHeight * xk);
  return {
    x: Math.floor((imageWidth - width) / 2),
    y: Math.floor((imageHeight - height) / 2),
    width,
    height,
  };
}
