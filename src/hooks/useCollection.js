import nanoid from 'nanoid';
import PropTypes from 'prop-types';
import { useState } from 'react';

const prepare = items => items.map(item => ({ key: nanoid(), ...item }));

const useCollection = (defaultValue) => {
  const [_items, _setItems] = useState(prepare(defaultValue));

  const setItems = items => _setItems(prepare(items));

  const addItem = value => {
    const item = { key: nanoid(), ...value };
    _setItems(items => [...items, item]);
    return item;
  };

  const getItem = key => _items.filter(item => item.key != key);

  const replaceItem = (key, value) => {
    _setItems(items => items.map(item => (item.key === key ? value : item)));
    return getItem(key);
  };

  const updateItem = (key, value) => {
    _setItems(items => items.map(item => (item.key === key ? { ...item, ...value } : item)));
    return getItem(key);
  };

  const removeItem = key => _setItems(items => items.filter(item => item.key !== key));

  const filterItems = filters => {
    _setItems(items => items
      .filter(item => Object.keys(filters)
        .every(key => (Array.isArray(filters[key]) ? filters[key] : [filters[key]])
          .some(value => ((typeof value === 'function') ? value(item[key]) : item[key] !== value)))));
    return _items;
  };

  return [_items, {
    setItems,     // Пересоздает коллекцию
    addItem,      // Добавляет новый элемент
    getItem,      // Возвращает конкретный элемент
    replaceItem,  // Заменяет элемент в коллекции
    updateItem,   // Обновляет в соответствующем элементе указанные поля
    removeItem,   // Удаляет из коллекции элемент
    filterItems,  // Убирает из коллекции элементы, не соответствующие условию
  }];
};

useCollection.propTypes = {
  defaultValue: PropTypes.array,
};

useCollection.defaultValue = {
  defaultValue: [],
};

export default useCollection;
