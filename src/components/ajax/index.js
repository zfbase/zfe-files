import React from 'react';
import ReactDOM from 'react-dom';
import $ from 'jquery';

import Element from './Element';

const getProps = node => {
  const props = {};
  for (let i = 0; i < node.attributes.length; i++) {
    if (/^data-/.test(node.attributes[i].name)) {
      const keyArr = /^data-(.*)/[Symbol.replace](node.attributes[i].name, '$1').split('-');
      const key = [keyArr.shift(), ...keyArr.map(k => k.substr(0, 1).toUpperCase() + k.substr(1).toLowerCase())].join('');
      props[key] = node.attributes[i].value;
    }
  }
  props.multiple = ['1', 'multiple'].includes(props.multiple);
  return props;
}

/** @todo Переписать без jQuery */
export default (root) => {
  const $root = $(root);
  const name = $root.data('name');

  const files = $.makeArray($root.find(`input[name^=${name}]`).map((i, input) => {
    const $input = $(input);
    return {
      id: $input.val(),
      ...$input.data(),
    };
  }));

  ReactDOM.render(<Element files={files} {...getProps(root)} />, root);
}
