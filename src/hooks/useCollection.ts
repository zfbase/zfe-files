import { nanoid } from 'nanoid';
import { useState } from 'react';

type SetItems<T extends object> = (items: T[]) => void;
type AddItem<T extends object> = (item: T) => T;
type GetItem<T extends object> = (key: string) => T | undefined;
type ReplaceItem<T extends object> = (key: string, value: T) => T | undefined;
type UpdateItem<T extends object> = (
  key: string,
  value: Partial<T>,
) => T | undefined;
type RemoveItem = (key: string) => void;
type FilterItems<T extends object> = (filter: (value: T) => boolean) => T[];

function prepare<T extends object>(items: T[]) {
  return items.map((item) => ({ key: nanoid(), ...item }));
}

export function useCollection<T extends object>(
  defaultValue: T[],
): [
  T[],
  {
    addItem: AddItem<T>;
    filterItems: FilterItems<T>;
    getItem: GetItem<T>;
    removeItem: RemoveItem;
    replaceItem: ReplaceItem<T>;
    setItems: SetItems<T>;
    updateItem: UpdateItem<T>;
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

  const removeItem: RemoveItem = (key) =>
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
