import { createRoot } from 'react-dom/client';
import { FileAjaxElement } from './FileAjaxElement';

const numberProps = ['itemId', 'maxChunkSize', 'maxFileSize'];

const getProps = (node: HTMLElement) => {
  const props: Record<string, string | number | boolean> = {};
  for (let i = 0; i < node.attributes.length; i += 1) {
    if (/^data-/.test(node.attributes[i].name)) {
      const keyArr = /^data-(.*)/
        [Symbol.replace](node.attributes[i].name, '$1')
        .split('-');
      const key = [
        keyArr.shift(),
        ...keyArr.map(
          (k) => k.substring(0, 1).toUpperCase() + k.substring(1).toLowerCase()
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
  props.multiple =
    typeof props.multiple === 'string' &&
    ['1', 'multiple'].includes(props.multiple);
  return props;
};

export function createFileAjax(root: HTMLElement, customProps: object) {
  const form = root.closest('form');
  const { name } = root.dataset;

  const files = Array.from(
    root.querySelectorAll<HTMLInputElement>(`input[name^=${name}]`)
  ).map((input) => {
    const data: Record<string, string | number | undefined> = input.dataset;
    const options: Record<string, unknown> & { data: typeof data } = { data };
    Object.keys(data).forEach((key) => {
      const parsed =
        typeof data[key] === 'string' ? parseInt(data[key] ?? '', 10) : '';
      const value = parsed.toString() === data[key] ? parsed : data[key];
      if (/^data/.test(key)) {
        options.data[key.charAt(4).toLowerCase() + key.substring(5)] = value;
      } else {
        options[key] = value;
      }
    });
    return {
      id: input.value,
      ...options,
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

  const props = {
    files,
    onLoaded: getOnLoadedHandler(),
    form,
    ...getProps(root),
    ...customProps,
  };

  const reactRoot = createRoot(root);
  reactRoot.render(<FileAjaxElement {...(props as any)} />);
}
