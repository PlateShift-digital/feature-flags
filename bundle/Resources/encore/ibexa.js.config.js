const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry(
        'plateshift-feature-flag-dashboard-js',
        [
            path.resolve(__dirname, '../assets/javascript/components/dashboard.js'),
        ]
    )
};
