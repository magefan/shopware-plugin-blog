/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import template from './blog-category-detail.html.twig';
import slug from "slug";

const {Component, Context, Mixin, Data: {Criteria}} = Shopware;

const {mapPropertyErrors} = Shopware.Component.getComponentHelper();

Component.register('blog-category-detail', {
    template,

    inject: ['repositoryFactory', 'acl'],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder')
    ],

    shortcuts: {
        'SYSTEMKEY+S': 'onSave',
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
            category: null,
            rootCategoryBlog: {},
            isLoading: false,
            isSaveSuccessful: false,
            isChangedLanguage: Shopware.Context.api.languageId,
            isNew: true,
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(this.identifier),
        };
    },

    computed: {
        identifier() {
            return this.placeholder(this.category, 'title');
        },

        categoryIsLoading() {
            return this.isLoading || this.category == null;
        },

        categoryMenuRepository() {
            return this.repositoryFactory.create('category');
        },

        categoryRepository() {
            return this.repositoryFactory.create('magefanblog_category');
        },

        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        mediaUploadTag() {
            return `blog-category-detail--${this.category.id}`;
        },

        tooltipSave() {
            if (this.acl.can('blog_category.editor')) {
                const systemKey = this.$device.getSystemKey();

                return {
                    message: `${systemKey} + S`,
                    appearance: 'light',
                };
            }

            return {
                showDelay: 300,
                message: this.$tc('sw-privileges.tooltip.warning'),
                disabled: this.acl.can('blog_category.editor'),
                showOnDisabledElements: true,
            };
        },

        tooltipCancel() {
            return {
                message: 'ESC',
                appearance: 'light',
            };
        },

        defaultCriteria() {
            return new Criteria(this.page, this.limit);
        },

        ...mapPropertyErrors('category', ['title']),
    },

    watch: {
        'category.title': function (value) {
            if (typeof value !== 'undefined') {
                let postIdentifier = slug(this.category.title, '-');
                this.buildIdentifier(postIdentifier, 1)
            }
        },
        id() {
            this.createdComponent();
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            if (this.id) {
                this.loadEntityData();
                return;
            }

            if (Shopware.Context.api.languageId !== Shopware.Context.api.systemLanguageId) {
                Shopware.State.commit('context/setApiLanguageId', Shopware.Context.api.languageId)
            }

            if (!Shopware.State.getters['context/isSystemDefaultLanguage']) {
                Shopware.State.commit('context/resetLanguageToDefault');
            }

            this.category = this.categoryRepository.create();
        },

        loadEntityData() {
            this.isNew = false;
            this.categoryRepository.get(this.id).then((category) => {
                this.isLoading = false;
                this.category = category;
            }).catch(() => {
                this.isLoading = false;
            });
        },

        setMediaItem({targetId}) {
            this.category.mediaId = targetId;
        },


        setMediaFromSidebar(media) {
            this.category.mediaId = media.id;
        },

        onUnlinkLogo() {
            this.category.mediaId = null;
        },


        openMediaSidebar() {
            this.$refs.mediaSidebarItem.openContent();
        },

        onDropMedia(dragData) {
            this.setMediaItem({targetId: dragData.id});
        },

        updateCategoryMenu() {
            this.getRootBlogCategory().then((rootCategory) => {
                this.categoryMenuRepository.get(this.category.id).then((category) => {
                    if (rootCategory[0].id && !category) {
                        const category = this.categoryMenuRepository.create();
                        category.id = this.category.id;
                        category.type = 'link';
                        category.parentId = rootCategory[0].id;
                        category.name = this.category.title
                        category.linkType = 'external';
                        category.externalLink = rootCategory[0].externalLink + '/category/' + this.category.id;
                        category.level = 3;
                        category.active = Boolean(this.category.isActive && this.category.includeInMenu);
                        this.categoryMenuRepository.save(category, Context.api);
                    } else if(rootCategory[0].id && category) {
                        this.categoryMenuRepository.get(this.category.id).then((category) => {
                            category.id = this.category.id;
                            category.name = this.category.title
                            category.externalLink = rootCategory[0].externalLink + '/category/' + this.category.id;
                            category.level = 3;
                            category.active = Boolean(this.category.isActive && this.category.includeInMenu);
                            this.categoryMenuRepository.save(category, Context.api);
                        });
                    }
                })
            });
        },

        getRootBlogCategory() {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('name', 'Blog'));
            criteria.addFilter(Criteria.equals('linkType', 'external'));
            return this.categoryMenuRepository.search(criteria).then((result) => {
                return result;
            })
        },

        onSave() {
            this.isLoading = true;
            this.categoryRepository.save(this.category, Context.api).then(() => {
                this.updateCategoryMenu()
                this.isLoading = false;
                this.isSaveSuccessful = true;
                if (this.category.id) {
                    this.$router.push({name: 'blog.category.detail', params: {id: this.category.id}});
                }
            })
                .catch((exception) => {
                    this.isLoading = false;
                    this.isSaveSuccessful = false;
                    this.createNotificationError({
                        message: this.$tc(
                            'global.notification.notificationSaveErrorMessageRequiredFieldsInvalid',
                        ),
                    });
                    throw exception;
                });
        },

        onChangeLanguage(languageId) {

            Shopware.State.commit('context/setApiLanguageId', languageId);

            this.isChangedLanguage = languageId;
            this.loadEntityData();
        },

        onCancel() {
            this.$router.push({name: 'blog.category.list'});
        },

        buildIdentifier(finalIdentifier, number){
            let numberItem = (number > 1 ? '-' + number : '');
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('identifier', finalIdentifier + numberItem));
            return this.categoryRepository.search(criteria, Shopware.Context.api).then((result) => {
                if(result.length === 0){
                    return this.category.identifier = slug(finalIdentifier + numberItem, '-');
                }else {
                    number++;
                    this.buildIdentifier(finalIdentifier, number);
                }
            });
        },
    }
});
