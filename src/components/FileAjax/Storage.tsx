import React, { Fragment } from 'react';
import PropTypes from 'prop-types';


export const LiteRender = ({ item, name }) => <input type="hidden" name={`${name}[]`} value={item.id} />;

LiteRender.propTypes = {
  item: PropTypes.shape({
    id: PropTypes.oneOfType([
      PropTypes.number,
      PropTypes.string,
    ]).isRequired,
  }).isRequired,
  name: PropTypes.string.isRequired,
};


export const ExtendedRender = ({ item, name }) => (
  <input
    type="hidden"
    name={`${name}[]`}
    value={JSON.stringify({
      id: item.id,
      ...item.data,
    })}
  />
);

ExtendedRender.propTypes = {
  item: PropTypes.object.isRequired,
  name: PropTypes.string.isRequired,
};


const Storage = ({ name, items, render }) => {
  const filteredItems = items.filter(item => item.id && !item.deleted);
  return (
    <Fragment>
      {filteredItems.map(item => render(item, name))}
      {(filteredItems.length === 0) && <input type="hidden" name={`${name}[]`} key="0" />}
    </Fragment>
  );
};

Storage.propTypes = {
  name: PropTypes.string.isRequired,
  items: PropTypes.arrayOf(
    PropTypes.object,
  ),
  render: PropTypes.func,
};

Storage.defaultProps = {
  items: [],
  render: (item, name) => (
    item.data
      ? <ExtendedRender item={item} name={name} key={item.id} />
      : <LiteRender item={item} name={name} key={item.id} />
  ),
};

export default Storage;
