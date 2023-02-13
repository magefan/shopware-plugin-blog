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

    _showTarget(target) {
        let parentCommentInput = DomAccess.querySelector(document, '#parentIdReply');
        parentCommentInput.value = target.classList[1].split('c-post-')[1] || '';

        let replyCommentDiv = DomAccess.querySelector(document, '#c-replyform-comment');
        let replyCommentForm = DomAccess.querySelector(document, '.reply-form-blog-comment');
        replyCommentForm.reset();
        target.appendChild(replyCommentDiv);
        replyCommentDiv.classList.remove('d-none');
    }

    _onClick() {
        const node = this.el.closest('.c-comment-parent-0 > .c-post');
        this._showTarget(node);
    }
}