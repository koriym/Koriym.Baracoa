import React from 'react';
import PropTypes from 'prop-types';

const Hello = ({ name, onClick }) => (
  <div>
    <h1>Hello { name }</h1>
    <button onClick={onClick}>Click</button>
  </div>
);

Hello.propTypes = {
  name: PropTypes.string.isRequired,
  onClick: PropTypes.func.isRequired,
};

export default Hello;
