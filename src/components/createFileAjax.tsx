import { createRoot } from 'react-dom/client';

import { FileAjax } from './FileAjax';

const numberProps = ['itemId', 'maxChunkSize', 'maxFileSize'];

const getProps = (node: HTMLElement) => {
  const props = {};

  for (let i = 0; i < node.attributes.length; i += 1) {
    if (/^data-/.test(node.attributes[i].name)) {
      const keyArr = /^data-(.*)/
        [Symbol.replace](node.attributes[i].name, '$1')
        .split('-');
      const key = [
        keyArr.shift(),
        ...keyArr.map(
          (k) => k.substring(0, 1).toUpperCase() + k.substring(1).toLowerCase(),
        ),
      ].join('');
      const { value } = node.attributes[i];
      props[key] = numberProps.includes(key) ? parseInt(value, 10) : value;
    } else if (node.attributes[i].name === 'disabled') {
      const { value } = node.attributes[i];
      if (value === 'disabled' || value === '1') {
        props.disabled = true;
      }
    }
  }

  props.multiple = ['1', 'multiple'].includes(props.multiple);
  return props;
};

export function createFileAjax(root: HTMLElement, customProps: object) {
  const form = root.closest('form');
  const { name } = root.dataset;

  const files = Array.from(
    root.querySelectorAll<HTMLInputElement>(`input[name^=${name}]`),
  ).map((input) => {
    const data = input.dataset;

    const options: {
      data: Record<string, number | string>;
      other: Record<string, number | string>;
    } = { data: {}, other: {} };

    Object.keys(data).forEach((key) => {
      const item = data[key];
      if (!item) {
        return;
      }
      const parsed = parseInt(item ?? '', 10);
      const value = parsed.toString() === item ? parsed : item;
      if (/^data/.test(key)) {
        options.data[key.charAt(4).toLowerCase() + key.substring(5)] = value;
      } else {
        options.other[key] = value;
      }
    });
    return {
      id: input.value,
      ...options,
    };
  });

  const getOnLoadedHandler = () => {
    const wnd = window as any;
    if (typeof wnd.jQuery === 'undefined') {
      return () => {};
    }
    const $form = wnd.jQuery(form);

    if (!$form.data('plugin_checkUnsavedFormData')) {
      return () => {};
    }

    return () => {
      $form.checkUnsavedFormData('setFreeValue', `${name}[0]`, null);
      $form.checkUnsavedFormData('setFreeValue', `${name}[]`);
    };
  };

  const props = {
    files,
    onLoaded: getOnLoadedHandler(),
    form,
    ...getProps(root),
    ...customProps,
  };

  const reactRoot = createRoot(root);
  // eslint-disable-next-line react/jsx-props-no-spreading
  reactRoot.render(<FileAjax {...props} />);
}
