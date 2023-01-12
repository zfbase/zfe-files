import React from 'react';
import ReactDOM from 'react-dom';

import Element from './Element';

const numberProps = [
  'itemId',
  'maxChunkSize',
  'maxFileSize',
];

const getProps = (node) => {
  const props = {};
  for (let i = 0; i < node.attributes.length; i += 1) {
    if (/^data-/.test(node.attributes[i].name)) {
      const keyArr = /^data-(.*)/[Symbol.replace](node.attributes[i].name, '$1').split('-');
      const key = [
        keyArr.shift(),
        ...keyArr.map((k) => k.substr(0, 1).toUpperCase() + k.substr(1).toLowerCase()),
      ].join('');
      const value = node.attributes[i].value;
      props[key] = numberProps.includes(key) ? parseInt(value, 10) : value;
    }
  }
  props.multiple = ['1', 'multiple'].includes(props.multiple);
  return props;
};

export default (root, customProps) => {
  const form = root.closest('form');
  const { name } = root.dataset;

  const files = Array.from(root.querySelectorAll(`input[name^=${name}]`)).map((input) => {
    const data = input.dataset;
    const options = { data: {} };
    Object.keys(data).forEach((key) => {
      const parsed = parseInt(data[key], 10);
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
    if (typeof window.jQuery === 'undefined') {
      return () => {};
    }
    const $form = window.jQuery(form);

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

  // eslint-disable-next-line react/jsx-props-no-spreading
  ReactDOM.render(<Element {...props} />, root);
};
