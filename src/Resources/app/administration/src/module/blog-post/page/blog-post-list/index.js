/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

const {Component, Mixin} = Shopware;
const {Criteria} = Shopware.Data;

import template from './blog-post-list.html.twig';

Component.register('blog-post-list', {
    template,

    inject: [
        'repositoryFactory',
        'acl'
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    data() {
        return {
            posts: null,
            repository: {},
            isLoading: true,
            cloning: false,
            sortBy: 'createdAt',
            sortDirection: 'DESC',
            naturalSorting: false,
            total: 0,
            limit: 10,
            page: 1,
            showDeleteModal: false,
            searchConfigEntity: 'magefanblog_post',
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(),
        };
    },

    computed: {
        postRepository() {
            return this.repositoryFactory.create('magefanblog_post');
        },

        commentRepository() {
            return this.repositoryFactory.create('magefanblog_comment');
        },

        columns() {
            return [
                {
                    property: 'title',
                    dataIndex: 'title',
                    routerLink: 'blog.post.detail',
                    label: this.$t('blog-post.list.labelTitle'),
                    inlineEdit: 'string',
                    allowResize: true,
                    sortable: false,
                },
                {
                    property: 'identifier',
                    dataIndex: 'identifier',
                    label: this.$t('blog-post.list.labelUrlKey'),
                    inlineEdit: 'string',
                    allowResize: true,
                    sortable: false,
                },
                {
                    property: 'categories',
                    dataIndex: 'categories',
                    label: this.$t('blog-post.list.labelCategory'),
                    allowResize: true,
                    sortable: false,
                },
                {
                    property: 'authorId',
                    dataIndex: 'author_id',
                    label: this.$t('blog-post.list.labelAuthor'),
                    allowResize: true,
                    sortable: false,
                },
                {
                    property: 'createdAt',
                    dataIndex: 'created_at',
                    label: this.$t('blog-post.list.labelCreatedAt'),
                    allowResize: true,
                    sortable: false,
                },
                {
                    property: 'updatedAt',
                    dataIndex: 'updated_at',
                    label: this.$t('blog-post.list.labelUpdatedAt'),
                    allowResize: true,
                    sortable: false,
                },
                {
                    property: 'isActive',
                    dataIndex: 'is_active',
                    label: this.$t('blog-post.list.labelActive'),
                    inlineEdit: 'boolean',
                    allowResize: true,
                    type: 'bool',
                    align: 'center',
                    sortable: false,
                }];
        },
    },

    created() {
        this.getList();
    },

    beforeRouteLeave(to, from, next) {
        this.$nextTick(() => {
            next();
        });
    },

    methods: {

        onDelete(id) {
            this.showDeleteModal = id;
        },

        onCloseDeleteModal() {
            this.showDeleteModal = false;
        },

        onConfirmDelete(id) {
            this.showDeleteModal = false;

            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('postId', id));
            this.commentRepository.search(criteria).then((comments) => {
                if (comments) {
                    for (const comment of comments) {
                        this.commentRepository.delete(comment.id,  Shopware.Context.api).then(() => {})
                    }
                }
            })
            this.postRepository.delete(id, Shopware.Context.api).then(() => {
                this.getList();
            })
        },

        onPageChange({page, limit}) {
            this.page = page;
            this.limit = limit;
            this.$emit('page-change');
        },

        getList() {
            this.isLoading = true;

            const criteria = new Criteria(this.page, this.limit);
            criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection, this.naturalSorting));
            criteria.addAssociation('postCategories');
            criteria.addAssociation('postAuthor');

            this.repository = this.repositoryFactory.create('magefanblog_post');
            this.repository.search(criteria, Shopware.Context.api).then((result) => {
                this.posts = result;
                this.total = result.total;
                this.isLoading = false;
            })
        },

        onChangeLanguage(languageId) {

            Shopware.State.commit('context/setApiLanguageId', languageId);

            this.getList();
        },

        updateTotal({total}) {
            this.total = total;
        },
        onDuplicate(referencePost) {
            this.post = referencePost;
            this.cloning = true;
        },

        onDuplicateFinish(duplicate) {
            this.cloning = false;
            this.post = null;

            this.$nextTick(() => {
                this.$router.push({name: 'blog.post.detail', params: {postId: duplicate.id}});
            });
        },
    }
});
