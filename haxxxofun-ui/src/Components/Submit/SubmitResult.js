import React, {Component} from 'react';
import {okOrThrow, post} from "../common/Http";
import {Navigate} from "react-router-dom";

class SubmitResult extends Component {
    constructor(props) {
        super(props);
        this.state = {
            loading: false,
            error: false,
            success: false,
            task: '',
            username: '',
            secret: ''
        };
    }

    submitDisabled() {
        return this.state.task === '' ||
            this.state.username === '' ||
            this.state.secret === '';
    }

    submit(e) {
        e.preventDefault();
        this.setState({
            loading: true,
            error: false
        }, () => {
            fetch(`${process.env.REACT_APP_API_URL}/result`, post({
                task: this.state.task,
                username: this.state.username,
                secret: this.state.secret
            }))
                .then(okOrThrow)
                .then(() => this.setState({success: true}))
                .catch(() => {
                    this.setState({error: true, loading: false});
                });
        });
    }

    render() {
        return (
            this.state.success ? <Navigate to="/"/> :
            <div className="container">
                <h2>Submit solution</h2>
                <div className="row">
                    <div className="col-md-6">
                        <form>
                            <div className="row-form">
                                <label htmlFor="task">Task</label>
                                <select id="task" value={this.state.task}
                                        onChange={e => this.setState({task: e.target.value})}>
                                    <option value="">-- pick --</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                </select>
                            </div>
                            <div className="row-form">
                                <label htmlFor="username">Username</label>
                                <input type="text" id="username"
                                       value={this.state.username}
                                       onChange={e => this.setState({username: e.target.value})}/>
                            </div>
                            <div className="row-form">
                                <label htmlFor="secret">Secret</label>
                                <input type="text" id="secret"
                                       value={this.state.secret}
                                       onChange={e => this.setState({secret: e.target.value})}/>
                            </div>
                            {this.state.loading ?
                                <span className="primary spinner"/> :
                                <button className="bordered shadowed tertiary"
                                        disabled={this.submitDisabled()}
                                        onClick={e => this.submit(e)}>
                                    Submit!
                                </button>}
                        </form>
                    </div>
                    <div className="col-md-6">
                        {this.state.error ?
                            <img className="bordered" alt="NoNoNo" src="/nonono.png"/> : ''}
                    </div>
                </div>
            </div>
        )
    }
}

export default SubmitResult;