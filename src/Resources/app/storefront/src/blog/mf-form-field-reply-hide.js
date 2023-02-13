/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import Plugin from 'src/plugin-system/plugin.class';
import DomAccess from 'src/helper/dom-access.helper';

export default class MfFormFieldReplyShowPlugin extends Plugin {
    init() {
        this._registerEvents();
    }

    _registerEvents() {
        this.el.onclick = this._onClick.bind(this);
    }

    _onClick() {
        DomAccess.querySelector(document, '#c-replyform-comment').classList.add('d-none');
    }
}