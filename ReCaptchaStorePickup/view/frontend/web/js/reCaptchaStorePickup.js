/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'Magento_ReCaptchaFrontendUi/js/reCaptcha'
    ],
    function (Component) {
        'use strict';

        return Component.extend({

            /**
             * @inheritdoc
             */
            renderReCaptcha: function () {
                this.captchaInitialized = false;
                this._super();
            },
        });
    }
);
