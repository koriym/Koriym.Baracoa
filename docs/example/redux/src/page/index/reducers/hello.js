import * as types from '../constants/ActionTypes';

const initialState = {
  name: '',
};

const hello = (state = initialState, action) => {
  switch (action.type) {
    case types.HELLO:
      return Object.assign({}, state, { name: 'CSR' });
    default:
      return state;
  }
};

export default hello;
