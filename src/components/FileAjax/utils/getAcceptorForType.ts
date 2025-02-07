export function getAcceptForType(type?: string) {
  return type && ['audio', 'video', 'image'].includes(type)
    ? `${type}/*`
    : undefined;
}
