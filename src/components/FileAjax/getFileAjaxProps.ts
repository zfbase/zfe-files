const numberProps = ['itemId', 'maxChunkSize', 'maxFileSize'];

export function getRootProps(attributes: { name: string; value: string }[]) {
  const props: Record<string, string | number | boolean> = {};
  attributes.forEach(({ name, value }) => {
    if (name.startsWith('data-')) {
      const keyArr = name.split('-').slice(1);
      const key = [
        keyArr.shift(),
        ...keyArr.map(
          (k) => k.substring(0, 1).toUpperCase() + k.substring(1).toLowerCase()
        ),
      ].join('');
      props[key] = numberProps.includes(key) ? parseInt(value, 10) : value;
    } else if (name === 'disabled') {
      if (value === 'disabled' || value === '1') {
        props.disabled = true;
      }
    }
  });
  props.multiple =
    typeof props.multiple === 'string' &&
    ['1', 'multiple'].includes(props.multiple);
  return props;
}

export function getFileProps(data: Record<string, string | undefined>) {
  const options: Record<string, unknown> & {
    data: Record<string, string | number | undefined>;
  } = { data: {} };
  Object.keys(data).forEach((key) => {
    const parsed =
      typeof data[key] === 'string' ? parseInt(data[key] ?? '', 10) : '';
    const value = parsed.toString() === data[key] ? parsed : data[key];
    if (key.startsWith('data')) {
      options.data[key.charAt(4).toLowerCase() + key.substring(5)] = value;
    } else {
      options[key] = value;
    }
  });
  return options;
}

export function getFileAjaxProps(root: HTMLElement, customProps: object) {
  const form = root.closest('form');
  const { name } = root.dataset;

  const files = Array.from(
    root.querySelectorAll<HTMLInputElement>(`input[name^=${name}]`)
  ).map((input) => {
    return {
      id: input.value,
      ...getFileProps(input.dataset),
    };
  });

  const getOnLoadedHandler = () => {
    if (typeof (window as any).jQuery === 'undefined') {
      return () => {};
    }
    const $form = (window as any).jQuery(form);

    if (!$form.data('plugin_checkUnsavedFormData')) {
      return () => {};
    }

    return () => {
      $form.checkUnsavedFormData('setFreeValue', `${name}[0]`, null);
      $form.checkUnsavedFormData('setFreeValue', `${name}[]`);
    };
  };

  const rootAttributes = Array.from(root.attributes).map((i) => ({
    name: i.name,
    value: i.value,
  }));

  const props = {
    files,
    onLoaded: getOnLoadedHandler(),
    form,
    ...getRootProps(rootAttributes),
    ...customProps,
  };

  return props;
}
