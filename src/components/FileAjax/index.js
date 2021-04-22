import React from 'react';
import ReactDOM from 'react-dom';
import $ from 'jquery';

import Element from './Element';

const getProps = (node) => {
  const props = {};
  for (let i = 0; i < node.attributes.length; i += 1) {
    if (/^data-/.test(node.attributes[i].name)) {
      const keyArr = /^data-(.*)/[Symbol.replace](node.attributes[i].name, '$1').split('-');
      const key = [keyArr.shift(), ...keyArr.map(k => k.substr(0, 1).toUpperCase() + k.substr(1).toLowerCase())].join('');
      props[key] = node.attributes[i].value;
    }
  }
  props.multiple = ['1', 'multiple'].includes(props.multiple);
  return props;
};

export default (root) => {
  const $root = $(root);
  const name = $root.data('name');

  const files = $.makeArray($root.find(`input[name^=${name}]`).map((i, input) => {
    const $input = $(input);
    const data = $input.data();
    const options = { data: {} };
    Object.keys(data).forEach((key) => {
      if (/^data/.test(key)) {
        options.data[key.substr(4, 1)[0].toLowerCase() + key.substr(5)] = data[key];
      } else {
        options[key] = data[key];
      }
    });
    return {
      id: $input.val(),
      ...options,
    };
  }));

  const getOnLoadedHandler = () => {
    const $form = $root.closest('form');

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
    form: $root.closest('form')[0],
    ...getProps(root),
  };

  ReactDOM.render(<Element {...props} />, root);
};
