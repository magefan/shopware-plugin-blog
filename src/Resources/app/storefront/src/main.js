/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import MfFormFieldShowPlugin from './blog/mf-form-field-show';
import MfFormAjaxSubmitPlugin from './blog/mf-form-ajax-submit';
import MfFormFieldReplyShowPlugin from "./blog/mf-form-field-reply-show";
import MfFormFieldReplyHidePlugin from "./blog/mf-form-field-reply-hide";
import MfFormAjaxSubmitReplyPlugin from "./blog/mf-form-ajax-reply-submit";
import MfFormShowMoreCommentsPlugin from "./blog/mf-form-show-more-comments";

const PluginManager = window.PluginManager;
PluginManager.register('MfFormFieldShow', MfFormFieldShowPlugin, '[data-mf-form-field-show]');
PluginManager.register('MfFormFieldReplyShowPlugin', MfFormFieldReplyShowPlugin, '[data-mf-form-field-show-reply]');
PluginManager.register('MfFormFieldReplyHidePlugin', MfFormFieldReplyHidePlugin, '.cancel.reply-cancel-action');
PluginManager.register('MfFormAjaxSubmit', MfFormAjaxSubmitPlugin, '[data-mf-form-ajax-submit]');
PluginManager.register('MfFormAjaxSubmitReplyPlugin', MfFormAjaxSubmitReplyPlugin, '[data-mf-form-ajax-reply-submit]');
PluginManager.register('MfFormShowMoreCommentsPlugin', MfFormShowMoreCommentsPlugin, '.c-allcomments.more-comments-action');