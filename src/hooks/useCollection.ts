import { nanoid } from 'nanoid';
import { useState } from 'react';

type SetItems<T extends {}> = (items: T[]) => void;
type AddItem<T extends {}> = (item: T) => T;
type GetItem<T extends {}> = (key: string) => T | undefined;
type ReplaceItem<T extends {}> = (key: string, value: T) => T | undefined;
type UpdateItem<T extends {}> = (
  key: string,
  value: Partial<T>,
) => T | undefined;
type RemoveItem<T extends {}> = (key: string) => void;
type FilterItems<T extends {}> = (filter: (value: T) => boolean) => T[];

function prepare<T extends {}>(items: T[]) {
  return items.map((item) => ({ key: nanoid(), ...item }));
}

function useCollection<T extends {}>(
  defaultValue: T[],
): [
  T[],
  {
    setItems: SetItems<T>;
    addItem: AddItem<T>;
    getItem: GetItem<T>;
    replaceItem: ReplaceItem<T>;
    updateItem: UpdateItem<T>;
    removeItem: RemoveItem<T>;
    filterItems: FilterItems<T>;
  },
] {
  const [_items, _setItems] = useState(() => prepare(defaultValue));

  const setItems: SetItems<T> = (items) => _setItems(prepare(items));

  const addItem: AddItem<T> = (value) => {
    const item = { key: nanoid(), ...value };
    _setItems((items) => [...items, item]);
    return item;
  };

  const getItem: GetItem<T> = (key) => _items.find((item) => item.key === key);

  const replaceItem: ReplaceItem<T> = (key, value) => {
    _setItems((items) =>
      items.map((item) => (item.key === key ? { key, ...value } : item)),
    );
    return getItem(key);
  };

  const updateItem: UpdateItem<T> = (key, value) => {
    _setItems((items) =>
      items.map((item) => (item.key === key ? { ...item, ...value } : item)),
    );
    return getItem(key);
  };

  const removeItem: RemoveItem<T> = (key) =>
    _setItems((items) => items.filter((item) => item.key !== key));

  const filterItems: FilterItems<T> = (filter) => {
    _setItems((items) => items.filter(filter));
    return _items;
  };

  return [
    _items,
    {
      setItems, // Пересоздает коллекцию
      addItem, // Добавляет новый элемент
      getItem, // Возвращает конкретный элемент
      replaceItem, // Заменяет элемент в коллекции
      updateItem, // Обновляет в соответствующем элементе указанные поля
      removeItem, // Удаляет из коллекции элемент
      filterItems, // Убирает из коллекции элементы, не соответствующие условию
    },
  ];
}

export default useCollection;
