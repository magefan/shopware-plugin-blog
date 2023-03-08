/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import template from './blog-tag-detail.html.twig';
import slug from "slug";

const {Component, Mixin} = Shopware;
const {Context, Data: {Criteria}} = Shopware;

Component.register('blog-tag-detail', {
    template,

    inject: [
        'repositoryFactory',
        'acl'
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder')
    ],

    shortcuts: {
        'SYSTEMKEY+S': {
            active() {
                return this.acl.can('blog_tag.editor');
            },
            method: 'onSave',
        },
        ESCAPE: 'onCancel',
    },

    props: {
        id: {
            type: String,
            default: null,
        },
    },

    data() {
        return {
            tag: null,
            isLoading: false,
            isSaveSuccessful: false,
            customFieldSets: null,
            isChangedLanguage: Shopware.Context.api.languageId,
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(this.identifier),
        };
    },

    computed: {
        identifier() {
            return this.placeholder(this.tag, 'title');
        },

        tagRepository() {
            return this.repositoryFactory.create('magefanblog_tag');
        },

        tooltipSave() {
            if (!this.acl.can('blog_tag.editor')) {
                return {
                    message: this.$tc('blog-tag.tooltip.warning'),
                    disabled: this.acl.can('blog_tag.editor'),
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

        defaultCriteria() {
            return new Criteria(this.page, this.limit);
        },
    },

    watch: {
        'tag.title': function (value) {
            if (typeof value !== 'undefined') {
                let postIdentifier = slug(this.tag.title, '-');
                this.buildIdentifier(postIdentifier, 1)
            }
        },
        id() {
            this.loadEntityData();
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.loadEntityData();
        },

        loadEntityData() {
            this.isLoading = true;

            this.tagRepository.get(this.id, Shopware.Context.api, this.defaultCriteria)
                .then((currentTag) => {
                    this.tag = currentTag;
                    this.isLoading = false;
                }).catch(() => {
                this.isLoading = false;
            });
        },

        onChangeLanguage(languageId) {

            Shopware.State.commit('context/setApiLanguageId', languageId);

            this.loadEntityData();
        },

        saveFinish() {
            this.isSaveSuccessful = false;
        },

        saveTag() {
            this.isLoading = true;

            return new Promise((resolve) => {
                this.tagRepository.save(this.tag).then(() => {
                    this.isLoading = false;
                    this.isSaveSuccessful = true;
                    resolve('success');
                }).catch((exception) => {
                    this.isLoading = false;
                    this.isSaveSuccessful = false;
                    this.createNotificationError({
                        message: this.$tc(
                            'global.notification.notificationSaveErrorMessageRequiredFieldsInvalid',
                        ),
                    });
                    throw exception;
                });
            });
        },

        onSave() {
            return this.saveTag();
        },

        onCancel() {
            this.$router.push({name: 'blog.tag.index'});
        },

        buildIdentifier(finalIdentifier, number) {
            let numberItem = (number > 1 ? '-' + number : '');
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('identifier', finalIdentifier + numberItem));
            return this.tagRepository.search(criteria, Shopware.Context.api).then((result) => {
                if (result.length === 0) {
                    return this.tag.identifier = slug(finalIdentifier + numberItem, '-');
                } else {
                    number++;
                    this.buildIdentifier(finalIdentifier, number);
                }
            });
        },
    },
});
