export type FormDataPayload = Record<string, string | Blob>;

export function createFormData(payload: FormDataPayload) {
  const formData = new FormData();
  Object.entries(payload).map(([key, value]) => formData.append(key, value));
  return formData;
}
