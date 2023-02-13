/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import Plugin from 'src/plugin-system/plugin.class';
import DomAccess from 'src/helper/dom-access.helper';
import Iterator from 'src/helper/iterator.helper';

export default class MfFormShowMoreCommentsPlugin extends Plugin {
    init() {
        this._getTargets();

        this._registerEvents();
    }

    _registerEvents() {
        this.el.onclick = this._onClick.bind(this);
    }

    _showTarget(target) {
        target.classList.remove('d-none');
    }

    _onClick() {
        this.el.remove();
        Iterator.iterate(this._targets, node => {
            this._showTarget(node);
        });
    }

    _getTargets() {
        const selector = DomAccess.getDataAttribute(this.el, 'data-mf-form-comments-field-show-target', false);
        this._targets = DomAccess.querySelectorAll(document, selector);
    }
}