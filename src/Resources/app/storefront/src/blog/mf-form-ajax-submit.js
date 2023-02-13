/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import Plugin from 'src/plugin-system/plugin.class';

export default class MfFormAjaxSubmitPlugin extends Plugin {
    init() {
        const plugin = window.PluginManager.getPluginInstanceFromElement(
            document.querySelector('[data-form-ajax-submit]'), 'FormAjaxSubmit'
        );
        plugin.$emitter.subscribe('onAfterAjaxSubmit', this._setContent);
    }

    _setContent(data) {
        if (data.target.querySelector('.alert')) {
            data.target.querySelector('.alert').remove();
        }

        data.target.insertAdjacentHTML('afterbegin', JSON.parse(data.detail.response).alert);
        data.target.reset();
        data.target.classList.remove('was-validated');

        setTimeout(function () {
            data.target.querySelector('.alert').remove();
        }, 4000);
    }
}