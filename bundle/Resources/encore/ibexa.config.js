const addJSEntries = require('./ibexa.js.config');
const addCSSEntries = require('./ibexa.css.config');

module.exports = (Encore) => {
    addJSEntries(Encore);
    addCSSEntries(Encore);
};
