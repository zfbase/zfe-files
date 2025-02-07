export function createFormData(params: Record<string, string | Blob | number>) {
  const formData = new FormData();
  Object.entries(params).map(([key, value]) =>
    formData.append(key, typeof value === 'number' ? value.toString() : value),
  );
  return formData;
}
