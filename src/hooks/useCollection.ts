import { nanoid } from 'nanoid';
import { useMemo, useState } from 'react';

function prepare<T extends {}>(items: T[]) {
  return items.map((item) => ({ key: nanoid(), ...item }));
}

export function useCollection<T extends {}>(defaultValue: T[]) {
  const [_items, _setItems] = useState(() => prepare(defaultValue));

  const methods = useMemo(() => {
    const setItems = (items: T[]) => _setItems(prepare(items));

    const addItem = (value: T) => {
      const item = { key: nanoid(), ...value };
      _setItems((items) => [...items, item]);
      return item;
    };

    const getItem = (key: string) => _items.find((item) => item.key === key);

    const replaceItem = (key: string, value: T) => {
      _setItems((items) =>
        items.map((item) => (item.key === key ? { ...value, key } : item)),
      );
      return getItem(key);
    };

    const updateItem = (key: string, value: T) => {
      _setItems((items) =>
        items.map((item) => (item.key === key ? { ...item, ...value } : item)),
      );
      return getItem(key);
    };

    const removeItem = (key: string) =>
      _setItems((items) => items.filter((item) => item.key !== key));

    const filterItems = (filters: Record<keyof T, unknown>) => {
      _setItems((items) =>
        items.filter((item) =>
          (Object.keys(filters) as (keyof T)[]).every((key) =>
            (Array.isArray(filters[key]) ? filters[key] : [filters[key]]).some(
              (value: unknown) =>
                typeof value === 'function'
                  ? value(item[key])
                  : item[key] !== value,
            ),
          ),
        ),
      );
      return _items;
    };

    return {
      setItems, // Пересоздает коллекцию
      addItem, // Добавляет новый элемент
      getItem, // Возвращает конкретный элемент
      replaceItem, // Заменяет элемент в коллекции
      updateItem, // Обновляет в соответствующем элементе указанные поля
      removeItem, // Удаляет из коллекции элемент
      filterItems, // Убирает из коллекции элементы, не соответствующие условию
    } as const;
  }, []);

  return [_items, methods] as const;
}
