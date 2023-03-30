import './App.css';
import {Component} from "react";
import {BrowserRouter, Route, Routes} from "react-router-dom";
import Dashboard from "./Components/Dashboard/Dashboard";
import SubmitResult from "./Components/Submit/SubmitResult";

class App extends Component {

    constructor(props) {
        super(props);
        this.state = {
            userData: undefined,
            logged: false,
        }
    }

    render() {
        return <BrowserRouter>
            <Routes>
                <Route path="/" element={<Dashboard/>}/>
                <Route path="submit" element={<SubmitResult/>}/>
            </Routes>
        </BrowserRouter>
    }
}

export default App;
