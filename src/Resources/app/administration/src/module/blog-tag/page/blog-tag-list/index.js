/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

const {Component, Mixin} = Shopware;
const {Context, Data: { Criteria } } = Shopware;

import template from './blog-tag-list.html.twig';

Component.register('blog-tag-list', {
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
            tags: null,
            repository: {},
            isLoading: true,
            cloning: false,
            sortBy: 'createdAt',
            sortDirection: 'DESC',
            naturalSorting: false,
            total: 0,
            searchConfigEntity: 'magefanblog_tag',
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(),
        };
    },

    computed: {
        tagRepository() {
            return this.repositoryFactory.create('magefanblog_tag');
        },

        columns() {
            return [
                {
                    property: 'title',
                    label: this.$t('blog-tag.list.labelTitle'),
                    routerLink: 'blog.tag.detail',
                    inlineEdit: 'string',
                    allowResize: true,
                    primary: true,
                },
                {
                    property: 'identifier',
                    dataIndex: 'identifier',
                    label: this.$t('blog-tag.list.labelUrlKey'),
                    inlineEdit: 'string',
                    allowResize: true,
                    sortable: false,
                },
                {
                    property: 'isActive',
                    dataIndex: 'is_active',
                    label: this.$t('blog-tag.list.labelActive'),
                    inlineEdit: 'boolean',
                    allowResize: true,
                    type: 'bool',
                    align: 'center',
                    sortable: false,
                }
            ];
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

        getList() {
            this.isLoading = true;

            const criteria = new Criteria(this.page, this.limit);
            criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection, this.naturalSorting));

            this.repository = this.repositoryFactory.create('magefanblog_tag');
            this.repository.search(criteria).then((result) => {
                this.tags = result;
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

        onDuplicate(referenceTag) {
            this.tag = referenceTag;
            this.cloning = true;
        },

        onDuplicateFinish(duplicate) {
            this.cloning = false;
            this.tag = null;

            this.$nextTick(() => {
                this.$router.push({name: 'blog.tag.detail', params: {tagId: duplicate.id}});
            });
        },
    },
});