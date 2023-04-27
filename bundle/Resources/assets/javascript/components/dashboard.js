import Table from './dashboard/table';

document.addEventListener('DOMContentLoaded', function _onLoad() {
    document.removeEventListener('DOMContentLoaded', _onLoad);

    (function (global, React, ReactDOM) {
        const dashboardTableBody = document.getElementById('plateshift-feature-flag-dashboard-table');
        const token = document.querySelector('meta[name="CSRF-Token"]').content;
        const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;

        ReactDOM.render(
            React.createElement(
                Table,
                Object.assign({
                    scopes: JSON.parse(dashboardTableBody.dataset.scopes),
                    featureDefinitions: JSON.parse(dashboardTableBody.dataset.featureDefinitions),
                    restInfo: {token, siteaccess}
                })
            ),
            dashboardTableBody
        );
    })(window, window.React, window.ReactDOM);
});
