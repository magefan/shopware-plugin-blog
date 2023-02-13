/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import template from './blog-comment-detail.html.twig';
import errorConfig from './error-config.json';

const { Component, Mixin, Data: { Criteria } } = Shopware;

const { mapPageErrors, mapPropertyErrors } = Shopware.Component.getComponentHelper();

Component.register('blog-comment-detail', {
    template,

    inject: ['repositoryFactory', 'acl'],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder')
    ],

    shortcuts: {
        'SYSTEMKEY+S': {
            active() {
                return this.acl.can('blog_comment.editor');
            },
            method: 'onSave',
        },
        ESCAPE: 'onCancel',
    },

    props: {
        id: {
            type: String,
            required: false,
            default: null,
        },
    },

    data() {
        return {
            comment: null,
            isLoading: false,
            isSaveSuccessful: false,
            STATUSES: Object.freeze({
                PENDING: 0,
                APPROVED: 1,
                NOT_APPROVED: 2,
            }),
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(this.identifier),
        };
    },

    computed: {
        identifier() {
            return this.placeholder(this.comment, 'authorNickname');
        },

        statusOptions() {
            return Object.entries(this.STATUSES).map(status => Object.create({
                label: this.$tc(`blog-comment.detail.statuses.${status[0].toLowerCase()}`),
                value: status[1],
            }));
        },

        commentRepository() {
            return this.repositoryFactory.create('magefanblog_comment');
        },

        commentCriteria() {
            return (new Criteria(1, 1))
                .addAssociation('post');
        },

        tooltipSave() {
            if (!this.acl.can('blog_comment.editor')) {
                return {
                    message: this.$tc('sw-privileges.tooltip.warning'),
                    disabled: this.acl.can('blog_comment.editor'),
                    showOnDisabledElements: true,
                };
            }

            const systemKey = this.$device.getSystemKey();

            return {
                message: `${systemKey} + S`,
                appearance: 'light',
            };
        },

        tooltipCancel() {
            return {
                message: 'ESC',
                appearance: 'light',
            };
        },

        ...mapPageErrors(errorConfig),
        ...mapPropertyErrors('comment', ['text'])
    },

    watch: {
        id() {
            this.loadEntityData();
        },
    },

    created() {
        this.loadEntityData();
    },

    methods: {
        loadEntityData() {
            return this.commentRepository.get(this.id, Shopware.Context.api, this.commentCriteria)
                .then((comment) => {
                    this.comment = comment;
                }).finally(() => {
                    this.isLoading = false;
                });
        },

        onSave() {
            this.isLoading = true;

            return this.commentRepository.save(this.comment).then(() => {
                this.isLoading = false;
                this.isSaveSuccessful = true;
            }).catch((exception) => {
                    this.isLoading = false;
                this.isSaveSuccessful = true;
                this.createNotificationError({
                        message: this.$tc('global.notification.notificationSaveErrorMessage', 0, {
                            entityName: this.comment.text,
                        }),
                    });
                    throw exception;
                })
        },

        saveFinish() {
            this.isSaveSuccessful = false;
        },

        onCancel() {
            this.$router.push({ name: 'blog.comment.index' });
        },
    },
});
