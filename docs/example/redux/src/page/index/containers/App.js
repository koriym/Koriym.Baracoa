import { connect } from 'react-redux';
import { hello } from '../actions';
import Hello from '../components/Hello';

const mapStateToProps = state => ({
  name: state.hello.name,
});

const mapDispatchToProps = dispatch => ({
  onClick: () => dispatch(hello()),
});

const App = connect(
  mapStateToProps,
  mapDispatchToProps,
)(Hello);

export default App;
