const path = require('path');

module.exports = (Encore) => {
    Encore.addStyleEntry(
        'plateshift-feature-flag-dashboard-css',
        [path.resolve(__dirname, '../assets/scss/dashboard.scss')]
    )
};
