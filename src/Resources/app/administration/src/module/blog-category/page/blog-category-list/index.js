/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

const {Component, Mixin} = Shopware;
const {Context, Data: { Criteria } } = Shopware;

import template from './blog-category-list.html.twig';

Component.register('blog-category-list', {
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
            categories: null,
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
            searchConfigEntity: 'magefanblog_category',
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(),
        };
    },

    computed: {
        categoryRepository() {
            return this.repositoryFactory.create('magefanblog_category');
        },

        categoryMenuRepository() {
            return this.repositoryFactory.create('category');
        },

        columns() {
            return [
                {
                    property: 'title',
                    label: this.$t('blog-category.list.labelTitle'),
                    routerLink: 'blog.category.detail',
                    inlineEdit: 'string',
                    allowResize: true,
                    primary: true,
                },
                {
                    property: 'identifier',
                    dataIndex: 'identifier',
                    label: this.$t('blog-category.list.labelUrlKey'),
                    inlineEdit: 'string',
                    allowResize: true,
                    sortable: false,
                },

                {
                    property: 'isActive',
                    dataIndex: 'is_active',
                    label: this.$t('blog-category.list.labelActive'),
                    inlineEdit: 'boolean',
                    allowResize: true,
                    type: 'bool',
                    align: 'center',
                    sortable: false
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

            return this.categoryRepository.delete(id).then(
                this.categoryMenuRepository.delete(id).then(() => {
                    this.getList();
                })
            );
        },

        getList() {
            this.isLoading = true;

            const criteria = new Criteria(this.page, this.limit);
            criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection, this.naturalSorting));

            this.repository = this.repositoryFactory.create('magefanblog_category');
            this.repository.search(criteria).then((result) => {
                this.categories = result;
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
        onDuplicate(referenceCategory) {
            this.category = referenceCategory;
            this.cloning = true;
        },

        onDuplicateFinish(duplicate) {
            this.cloning = false;
            this.category = null;

            this.$nextTick(() => {
                this.$router.push({name: 'blog.category.detail', params: {id: duplicate.id}});
            });
        },
    },
});