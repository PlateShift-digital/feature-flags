import React, {Fragment} from 'react';
import PropTypes from 'prop-types';
import FeatureFlagHandler from '../../handlers/feature_flag_handler';
import Row from './row';

export default class Table extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            loading: {
                loaded: false,
                reloading: false,
                error: false,
                errorMessage: '',
            },
            scope: 'global',
            features: [],
        }
    }

    onFeatureChangeStart() {
        this.setState(prevState => ({
            loading: {
                loaded: prevState.loading.loaded,
                reloading: true,
                error: prevState.loading.error,
                errorMessage: prevState.loading.error,
            },
            scope: prevState.scope,
            features: prevState.features,
        }));
    }

    onFeatureChangeFinished() {
        this.setState(prevState => ({
            loading: {
                loaded: prevState.loading.loaded,
                reloading: true,
                error: prevState.loading.error,
                errorMessage: prevState.loading.error,
            },
            scope: prevState.scope,
            features: prevState.features,
        }));

        this.updateFeatureFlagData();
    }

    componentDidMount() {
        this.updateFeatureFlagData();
    }

    componentDidUpdate(prevProps, prevState) {
        if (prevState.scope !== this.state.scope) {
            this.updateFeatureFlagData();
        }
    }

    updateFeatureFlagData() {
        FeatureFlagHandler.loadFeatureFlags(this.state.scope, (features, error, errorMessage) => {
            this.setState(prevState => ({
                loading: {
                    loaded: prevState.loading.loaded || error === false,
                    reloading: false,
                    error: error,
                    errorMessage: errorMessage,
                },
                scope: prevState.scope,
                features: features,
            }))
        });
    }

    changeScope(event) {
        const newScope = event.target.value;

        this.setState(prevState => ({
            loading: {
                loaded: prevState.loading.loaded,
                reloading: true,
                error: prevState.loading.error,
                errorMessage: prevState.loading.error,
            },
            scope: newScope,
            features: prevState.features,
        }));
    }

    renderScopeSelection() {
        return this.props.scopes.map((item, key) => {
            return (
                <option value={item} key={key}>{window.Translator.trans(`feature.scope.${item}`, {}, 'feature_flag')}</option>
            );
        })
    }

    renderRows() {
        if (!this.state.loading.loaded) {
            return (
                <tr>
                    <td colSpan="5"><em>{window.Translator.trans('table.column.row.loading', {}, 'feature_flag')}</em></td>
                </tr>
            );
        }

        const rows = [];

        this.state.features.forEach((item, key) => {
            rows.push(
                <Row
                    key={`${key}-${this.state.scope}`}
                    featureDefinition={item}
                    loading={this.state.loading.reloading}
                    onFeatureChangeStart={this.onFeatureChangeStart.bind(this)}
                    onFeatureChangeFinished={this.onFeatureChangeFinished.bind(this)}
                />
            );
        });

        return rows;
    }

    render() {
        return (
            <Fragment>
                <div className="ibexa-table-header mt-3">
                    <div
                        className="ibexa-table-header__headline">{window.Translator.trans('table.header.name', {}, 'feature_flag')}</div>
                    <div>
                        <select
                            className="form-control"
                            onChange={this.changeScope.bind(this)}
                            value={this.state.scope}
                            disabled={this.state.loading.reloading || !this.state.loading.loaded}
                        >
                            {this.renderScopeSelection()}
                        </select>
                    </div>
                </div>
                <table className="table feature-flag-table">
                    <thead>
                        <tr>
                            <th>{window.Translator.trans('table.column.head.name', {}, 'feature_flag')}</th>
                            <th>{window.Translator.trans('table.column.head.description', {}, 'feature_flag')}</th>
                            <th>{window.Translator.trans('table.column.head.state', {}, 'feature_flag')}</th>
                            <th>{window.Translator.trans('table.column.head.source', {}, 'feature_flag')}</th>
                            <th>{window.Translator.trans('table.column.head.change', {}, 'feature_flag')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {this.renderRows()}
                    </tbody>
                </table>
            </Fragment>
        );
    }
}

Table.propTypes = {
    scopes: PropTypes.arrayOf(PropTypes.string).isRequired
};
