const ENDPOINT_LOAD_LIST = '/api/ibexa/v2/feature-flag/list/';
const ENDPOINT_FEATURE = '/api/ibexa/v2/feature-flag';

class FeatureFlagHandler {
    constructor(siteaccess, token) {
        this.siteaccess = siteaccess;
        this.token = token;
    }

    loadFeatureFlags(scope, callback) {
        const request = new Request(
            `${ENDPOINT_LOAD_LIST}${scope}`,
            {
                method: 'GET',
                mode: 'same-origin',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-Siteaccess': this.siteaccess,
                    'X-CSRF-Token': this.token,
                },
            }
        );

        fetch(request)
            .then(ibexa.helpers.request.getJsonFromResponse)
            .then((data) => {
                callback(data, false, '');
            })
            .catch(ibexa.helpers.notification.showErrorNotification);
    }

    updateFeatureFlag(identifier, scope, state, callback) {
        const request = new Request(
            `${ENDPOINT_FEATURE}`,
            {
                method: 'POST',
                mode: 'same-origin',
                credentials: 'same-origin',
                body: JSON.stringify({
                    identifier, scope, state
                }),
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-Siteaccess': this.siteaccess,
                    'X-CSRF-Token': this.token,
                },
            }
        );

        fetch(request)
            .then(ibexa.helpers.request.getJsonFromResponse)
            .then(data => {
                ibexa.helpers.notification.showSuccessNotification(data.message);
            })
            .catch(ibexa.helpers.notification.showErrorNotification)
            .finally(callback);
    }

    resetFeatureFlag(identifier, scope, callback) {
        const request = new Request(
            `${ENDPOINT_FEATURE}`,
            {
                method: 'DELETE',
                mode: 'same-origin',
                credentials: 'same-origin',
                body: JSON.stringify({
                    identifier, scope
                }),
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-Siteaccess': this.siteaccess,
                    'X-CSRF-Token': this.token,
                },
            }
        );

        fetch(request)
            .then(ibexa.helpers.request.getJsonFromResponse)
            .then(data => {
                ibexa.helpers.notification.showSuccessNotification(data.message);
            })
            .catch(ibexa.helpers.notification.showErrorNotification)
            .finally(callback);
    }
}

const siteaccess = document.querySelector('meta[name="SiteAccess"]').getAttribute('content');
const token = document.querySelector('meta[name="CSRF-Token"]').getAttribute('content');

export default new FeatureFlagHandler(siteaccess, token);
