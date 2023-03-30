import React, {Component} from 'react';
import {toHoursAndMinutes} from "../common/Time";

class Task extends Component {
    constructor(props) {
        super(props);
        this.state = {
            task: props.task,
            results: props.results
        };
    }

    render() {
        return <>
            <h3>Task {this.state.task}</h3>
            {this.state.results() && this.state.results().length > 0 ?
                <ul>
                    {this.state.results().map((result, index) =>
                        <li key={index}>
                            <mark className="tag">{toHoursAndMinutes(result.epoch_time)}</mark>
                            &nbsp;{result.user}
                        </li>)}
                </ul> :
                <mark className="primary">This place waits for <b>you</b>.</mark>}
        </>
    }
}

export default Task;