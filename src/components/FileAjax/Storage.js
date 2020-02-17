import React, { Fragment } from 'react';
import PropTypes from 'prop-types';

const Storage = ({ name, items }) => {
  const filteredItems = items.filter(item => item.id && !item.deleted);
  return (
    <Fragment>
      {filteredItems.map(item => <input type="hidden" name={`${name}[]`} value={item.id} key={item.id} />)}
      {(filteredItems.length == 0) && <input type="hidden" name={`${name}[]`} key="0" />}
    </Fragment>
  );
};

Storage.propTypes = {
  name: PropTypes.string.isRequired,
  items: PropTypes.arrayOf(
    PropTypes.object,
  ),
};

Storage.defaultProps = {
  items: [],
};

export default Storage;
