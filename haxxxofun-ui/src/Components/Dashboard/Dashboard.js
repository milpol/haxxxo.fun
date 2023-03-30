import React, {Component} from 'react';
import {NavLink, useParams} from "react-router-dom";
import Task from "./Task";
import {toHoursAndMinutes} from "../common/Time";

class Dashboard extends Component {

    _tasksPinger = undefined;
    _finalsPinger = undefined;

    constructor(props) {
        super(props);
        this.state = {
            tasks: [],
            finals: []
        };
    }

    componentDidMount() {
        this.loadTasksResults();
        this._tasksPinger = setInterval(() =>
            this.loadTasksResults(), 60000);
        this.loadFinalsResults();
        this._finalsPinger = setInterval(() =>
            this.loadFinalsResults(), 5 * 60000);
    }

    loadTasksResults() {
        fetch(`${process.env.REACT_APP_API_URL}/results/tasks`)
            .then(response => response.json())
            .then(data => {
                this.setState({tasks: data});
            });
    }

    loadFinalsResults() {
        fetch(`${process.env.REACT_APP_API_URL}/results/final`)
            .then(response => response.json())
            .then(data => {
                this.setState({finals: data});
            });
    }

    componentWillUnmount() {
        clearInterval(this._tasksPinger);
        this._tasksPinger = undefined;
        clearInterval(this._finalsPinger);
        this._finalsPinger = undefined;
    }

    render() {
        return (
            <div>
                <h1>HaXxXo.fun / <small>Lab Days 23' (February edition)</small></h1>
                <hr/>
                <h2>Tasks</h2>
                <div className="button-group">
                    <a href="/tasks/1/" target="_blank" className="button">Task 1</a>
                    <a href="/tasks/2/" target="_blank" className="button">Task 2</a>
                    <a href="/tasks/3/" target="_blank" className="button">Task 3</a>
                    <a href="/tasks/4/" target="_blank" className="button">Task 4</a>
                    <a href="/tasks/5/" target="_blank" className="button">Task 5</a>
                    <a href="/tasks/6/" target="_blank" className="button">Task 6</a>
                    <NavLink className="button shadowed bordered tertiary" to='/submit'>Submit solution</NavLink>
                </div>
                <hr/>
                <h2>Result board</h2>
                <div className="container">
                    <div className="row">
                        <div className="col-md-2">
                            <Task task={1}
                                  results={() => this.state.tasks['task1']}/>
                        </div>
                        <div className="col-md-2">
                            <Task task={2}
                                  results={() => this.state.tasks['task2']}/>
                        </div>
                        <div className="col-md-2">
                            <Task task={3}
                                  results={() => this.state.tasks['task3']}/>
                        </div>
                        <div className="col-md-2">
                            <Task task={4}
                                  results={() => this.state.tasks['task4']}/>
                        </div>
                        <div className="col-md-2">
                            <Task task={5}
                                  results={() => this.state.tasks['task5']}/>
                        </div>
                        <div className="col-md-2">
                            <Task task={6}
                                  results={() => this.state.tasks['task6']}/>
                        </div>
                    </div>
                </div>
                <hr/>
                <h2>Grand finale</h2>
                {this.state.finals && this.state.finals.length > 0 ? <ul>
                    {this.state.finals.map((final, index) => <li key={index}>
                        <mark className="tertiary">{index + 1}</mark>
                        &nbsp;{final.user}&nbsp;
                        <mark className="tag">{toHoursAndMinutes(final.all_time)}</mark>
                    </li>)}
                </ul> : <p>
                    <mark className="primary">This place waits for <b>you</b>.</mark>
                </p>}
            </div>
        )
    }
}

const Module = (props) => (
    <Dashboard
        {...props}
        params={useParams()}
    />
);
export default Module;