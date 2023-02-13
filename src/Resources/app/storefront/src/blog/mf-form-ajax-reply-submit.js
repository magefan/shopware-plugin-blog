/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import Plugin from 'src/plugin-system/plugin.class';

export default class MfFormAjaxSubmitReplyPlugin extends Plugin {
    init() {
        const plugin = window.PluginManager.getPluginInstanceFromElement(
            document.querySelector('#comment-reply'), 'FormAjaxSubmit'
        );
        plugin.$emitter.subscribe('onAfterAjaxSubmit', this._setContent);
    }

    _setContent(data) {
        if (data.target.querySelector('.alert')) {
            data.target.querySelector('.alert').remove();
        }

        document.querySelector('#c-replyform-comment').classList.add('d-none');
        data.target.closest('.c-post').insertAdjacentHTML('beforeend', JSON.parse(data.detail.response).alert);
        data.target.reset();
        data.target.classList.remove('was-validated');

        setTimeout(function () {
            data.target.closest('.c-post').querySelector('.alert').remove();
        }, 4000);
    }
}