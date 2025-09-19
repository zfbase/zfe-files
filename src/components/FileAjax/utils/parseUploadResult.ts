function firstCase(s: string, upper: boolean): string {
  const first = s.substring(0, 1);
  return (
    (upper ? first.toLocaleUpperCase() : first.toLocaleLowerCase()) +
    s.substring(1)
  );
}

function camelCase(s: string): string {
  return s
    .split('-')
    .filter(Boolean)
    .map((p, i) => firstCase(p, i !== 0))
    .join('');
}

export function parseUploadResult(raw: Record<string, string | number>) {
  const res: Record<string, string | number> = {};
  const data: Record<string, string | number> = {};

  Object.keys(raw).forEach((key) => {
    if (key.startsWith('data')) {
      data[camelCase(key.substring(4))] = raw[key];
    } else {
      res[key] = raw[key];
    }
  });

  return { ...res, data };
}
