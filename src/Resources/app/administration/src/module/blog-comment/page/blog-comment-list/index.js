/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import template from './blog-comment-list.html.twig';
import './mf-blog-comment-list.scss';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('blog-comment-list', {
    template,

    inject: [
        'repositoryFactory',
        'acl',
        'filterFactory',
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('listing'),
        Mixin.getByName('placeholder'),
    ],

    data() {
        return {
            comments: null,
            sortBy: 'createdAt',
            sortDirection: 'DESC',
            isLoading: false,
            total: 0,
            comment: null,
            filterCriteria: [],
            defaultFilters: [
                'status-filter'
            ],
            storeKey: 'grid.filter.comment',
            activeFilterNumber: 0,
            searchConfigEntity: 'comment',
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(),
        };
    },

    computed: {
        commentRepository() {
            return this.repositoryFactory.create('magefanblog_comment');
        },

        commentColumns() {
            return this.getCommentColumns();
        },

        commentCriteria() {
            const commentCriteria = new Criteria(this.page, this.limit);

            commentCriteria.setTerm(this.term);
            commentCriteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection, this.naturalSorting));
            commentCriteria.addAssociation('post');

            this.filterCriteria.forEach(filter => {
                commentCriteria.addFilter(filter);
            });

            return commentCriteria;
        },

        listFilters() {
            return this.filterFactory.create('comment', {
                'status-filter': {
                    property: 'status',
                    label: this.$tc('mf-comment.filters.statusFilter.label'),
                    placeholder: this.$tc('mf-comment.filters.statusFilter.placeholder'),
                },
            });
        },
    },

    watch: {
        commentCriteria: {
            handler() {
                this.getList();
            },
            deep: true,
        },
    },

    beforeRouteLeave(to, from, next) {
        this.$nextTick(() => {
            next();
        });
    },

    methods: {
        async getList() {
            this.isLoading = true;

            let criteria = await Shopware.Service('filterService')
                .mergeWithStoredFilters(this.storeKey, this.commentCriteria);

            criteria = await this.addQueryScores(this.term, criteria);

            this.activeFilterNumber = criteria.filters.length - 1;

            if (!this.entitySearchable) {
                this.isLoading = false;
                this.total = 0;

                return;
            }

            if (this.freshSearchTerm) {
                criteria.resetSorting();
            }

            try {
                const result = await Promise.all([
                    this.commentRepository.search(criteria),
                ]);

                const comments = result[0];

                this.total = comments.total;
                this.comments = comments;

                this.isLoading = false;

                this.selection = {};
            } catch {
                this.isLoading = false;
            }
        },

        onInlineEditSave(promise, comment) {
            const text = comment.text || this.placeholder(comment, 'text');

            return promise.then(() => {
                this.createNotificationSuccess({
                    message: this.$tc('sw-product.list.messageSaveSuccess', 0, { text: text }),
                });
            }).catch(() => {
                this.getList();
                this.createNotificationError({
                    message: this.$tc('global.notification.notificationSaveErrorMessageRequiredFieldsInvalid'),
                });
            });
        },

        onInlineEditCancel(comment) {
            comment.discardChanges();
        },

        updateTotal({ total }) {
            this.total = total;
        },

        updateCriteria(criteria) {
            this.page = 1;

            this.filterCriteria = criteria;
        },

        getCommentColumns() {
            return [{
                property: 'text',
                label: this.$t('blog-comment.list.labelText'),
                routerLink: 'blog.comment.detail',
                inlineEdit: 'string',
                allowResize: true,
                primary: true,
            }, {
                property: 'authorNickname',
                label: this.$t('blog-comment.list.labelNickName'),
                align: 'right',
                allowResize: true,
            }, {
                property: 'post.title',
                label: this.$t('blog-comment.list.labelPost'),
                allowResize: true,
            }, {
                property: 'status',
                label: this.$t('blog-comment.list.labelStatus'),
                align: 'center',
                allowResize: true,
            }, {
                property: 'createdAt',
                label: this.$t('blog-comment.list.labelPublished'),
                align: 'right',
                allowResize: true,
            }, {
                property: 'updatedAt',
                label: this.$t('blog-comment.list.labelModified'),
                align: 'right',
                allowResize: true,
            }];
        },

        onColumnSort(column) {
            this.onSortColumn(column);
        },
    },
});