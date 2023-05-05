/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import template from './blog-post-detail-base.html.twig';

const {Component, Mixin, Context} = Shopware;
const {Criteria} = Shopware.Data;
const { mapPropertyErrors } = Shopware.Component.getComponentHelper();

Component.register('blog-post-detail-base', {
    template,

    inject: ['repositoryFactory', 'acl'],

    mixins: [
        Mixin.getByName('placeholder'),
    ],

    props: {
        post: {
            type: Object,
            required: true,
            default() {
                return {};
            },
        },
        isChangedLanguage: {
            type: String,
            default: false,
        },
        isLoading: {
            type: Boolean,
            default: false,
        },
        allowEdit: {
            type: Boolean,
            required: false,
            default: true,
        },
    },

    data() {
        return {
            categories: [],
            categoriesSelected: [],
            activeDefault: 1,
        };
    },

    watch: {
        isChangedLanguage () {
            this.initValues();
        }
    },

    computed: {
        postRepository() {
            return this.repositoryFactory.create('magefanblog_post');
        },

        categoryRepository() {
            return this.repositoryFactory.create('magefanblog_category');
        },

        ...mapPropertyErrors('post', ['title']),
    },

    created() {
        this.initValues();
        if (!this.post.title){
            this.post.isActive = true;
        }
    },

    methods: {
        initValues() {
            this.getAllCategories();
            this.getSelectedCategories();
        },

        getAllCategories() {
            const criteria = new Criteria();
            this.categoryRepository.search(criteria, Shopware.Context.api).then((categories) => {
                const preparedCategories = [];
                for (let category of categories) {
                    preparedCategories.push({value: category.id, label: category.title})
                }
                this.categories = preparedCategories;
            })
        },

        getSelectedCategories() {
            const criteria = new Criteria();
            criteria.addAssociation('postCategories');
            criteria.addFilter(Criteria.equals('id', this.post.id));
            this.postRepository.search(criteria, Shopware.Context.api).then((post) => {
                const preparedSelectedCategories = [];
                if  (typeof(post[0]) != "undefined"){
                    for (let category of post[0].postCategories) {
                        preparedSelectedCategories.push(category.id);
                    }
                    this.categoriesSelected = preparedSelectedCategories;
                }
            })
        },

        updateSelectedCategories(categories) {
            this.categoriesSelected = categories;
            this.$emit('categories', this.categoriesSelected)
        },
    }
});
