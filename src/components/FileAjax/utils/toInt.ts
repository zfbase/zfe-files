export function toInt(v: number | string | undefined) {
  if (typeof v === 'number') {
    return v;
  }
  if (typeof v === 'string' && v.match(/^\d+$/)) {
    return parseInt(v);
  }
  return undefined;
}
