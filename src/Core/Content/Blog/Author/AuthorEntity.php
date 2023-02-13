<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\Author;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class AuthorEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string|null
     */
    protected ?string $firstName;

    /**
     * @var string|null
     */
    protected ?string $lastname;

    /**
     * @var string|null
     */
    protected ?string $email;

    /**
     * @var string|null
     */
    protected ?string $role;

    /**
     * @var string|null
     */
    protected ?string $facebookPageUrl;

    /**
     * @var string|null
     */
    protected ?string $twitterPageUrl;

    /**
     * @var string|null
     */
    protected ?string $instagramPageUrl;

    /**
     * @var string|null
     */
    protected ?string $googlePlusPageUrl;

    /**
     * @var string|null
     */
    protected ?string $linkedinPageUrl;

    /**
     * @var string|null
     */
    protected ?string $metaTitle;

    /**
     * @var string|null
     */
    protected ?string $metaKeywords;

    /**
     * @var string|null
     */
    protected ?string $metaRobots;

    /**
     * @var string|null
     */
    protected ?string $metaDescription;

    /**
     * @var string|null
     */
    protected ?string $identifier;

    /**
     * @var string|null
     */
    protected ?string $content;

    /**
     * @var string|null
     */
    protected ?string $shortContent;

    /**
     * @var string|null
     */
    protected ?string $featuredImg;

    /**
     * @var string|null
     */
    protected ?string $pageLayout;

    /**
     * @var string|null
     */
    protected ?string $layoutUpdateXml;

    /**
     * @var string|null
     */
    protected ?string $customTheme;

    /**
     * @var string|null
     */
    protected ?string $customLayout;

    /**
     * @var string|null
     */
    protected ?string $customLayoutUpdateXml;

    /**
     * @var string|null
     */
    protected ?string $customThemeFrom;

    /**
     * @var string|null
     */
    protected ?string $customThemeTo;

    /**
     * @var int|null
     */
    protected ?int $postsPerPage;

    /**
     * @var string|null
     */
    protected ?string $postsListTemplate;

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     * @return void
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     * @return void
     */
    public function setLastname(?string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return void
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * @param string|null $role
     * @return void
     */
    public function setRole(?string $role): void
    {
        $this->role = $role;
    }

    /**
     * @return string|null
     */
    public function getFacebookPageUrl(): ?string
    {
        return $this->facebookPageUrl;
    }

    /**
     * @param string|null $facebookPageUrl
     * @return void
     */
    public function setFacebookPageUrl(?string $facebookPageUrl): void
    {
        $this->facebookPageUrl = $facebookPageUrl;
    }

    /**
     * @return string|null
     */
    public function getTwitterPageUrl(): ?string
    {
        return $this->twitterPageUrl;
    }

    /**
     * @param string|null $twitterPageUrl
     * @return void
     */
    public function setTwitterPageUrl(?string $twitterPageUrl): void
    {
        $this->twitterPageUrl = $twitterPageUrl;
    }

    /**
     * @return string|null
     */
    public function getInstagramPageUrl(): ?string
    {
        return $this->instagramPageUrl;
    }

    /**
     * @param string|null $instagramPageUrl
     * @return void
     */
    public function setInstagramPageUrl(?string $instagramPageUrl): void
    {
        $this->instagramPageUrl = $instagramPageUrl;
    }

    /**
     * @return string|null
     */
    public function getGooglePlusPageUrl(): ?string
    {
        return $this->googlePlusPageUrl;
    }

    /**
     * @param string|null $googlePlusPageUrl
     * @return void
     */
    public function setGooglePlusPageUrl(?string $googlePlusPageUrl): void
    {
        $this->googlePlusPageUrl = $googlePlusPageUrl;
    }

    /**
     * @return string|null
     */
    public function getLinkedinPageUrl(): ?string
    {
        return $this->linkedinPageUrl;
    }

    /**
     * @param string|null $linkedinPageUrl
     * @return void
     */
    public function setLinkedinPageUrl(?string $linkedinPageUrl): void
    {
        $this->linkedinPageUrl = $linkedinPageUrl;
    }

    /**
     * @return string|null
     */
    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    /**
     * @param string|null $metaTitle
     * @return void
     */
    public function setMetaTitle(?string $metaTitle): void
    {
        $this->metaTitle = $metaTitle;
    }

    /**
     * @return string|null
     */
    public function getMetaKeywords(): ?string
    {
        return $this->metaKeywords;
    }

    /**
     * @param string|null $metaKeywords
     * @return void
     */
    public function setMetaKeywords(?string $metaKeywords): void
    {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * @return string|null
     */
    public function getMetaRobots(): ?string
    {
        return $this->metaRobots;
    }

    /**
     * @param string|null $metaRobots
     * @return void
     */
    public function setMetaRobots(?string $metaRobots): void
    {
        $this->metaRobots = $metaRobots;
    }

    /**
     * @param string|null $metaDescription
     * @return void
     */
    public function setMetaDescription(?string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * @return string|null
     */
    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    /**
     * @param string|null $identifier
     * @return void
     */
    public function setIdentifier(?string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return void
     */
    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string|null
     */
    public function getShortContent(): ?string
    {
        return $this->shortContent;
    }

    /**
     * @param $shortContent
     * @return void
     */
    public function setShortContent($shortContent)
    {
        $this->shortContent = $shortContent;
    }

    /**
     * @return string|null
     */
    public function getFeaturedImg(): ?string
    {
        return $this->featuredImg;
    }

    /**
     * @param $featuredImg
     * @return void
     */
    public function setFeaturedImg($featuredImg)
    {
        $this->featuredImg = $featuredImg;
    }

    /**
     * @return string|null
     */
    public function getPageLayout(): ?string
    {
        return $this->pageLayout;
    }

    /**
     * @param $pageLayout
     * @return void
     */
    public function setPageLayout($pageLayout)
    {
        $this->pageLayout = $pageLayout;
    }

    /**
     * @return string|null
     */
    public function getLayoutUpdateXml(): ?string
    {
        return $this->layoutUpdateXml;
    }

    /**
     * @param $layoutUpdateXml
     * @return void
     */
    public function setLayoutUpdateXml($layoutUpdateXml)
    {
        $this->layoutUpdateXml = $layoutUpdateXml;
    }

    /**
     * @return string|null
     */
    public function getCustomTheme(): ?string
    {
        return $this->customTheme;
    }

    /**
     * @param $customTheme
     * @return void
     */
    public function setCustomTheme($customTheme)
    {
        $this->customTheme = $customTheme;
    }

    /**
     * @return string|null
     */
    public function getCustomLayout(): ?string
    {
        return $this->customLayout;
    }

    /**
     * @param $customLayout
     * @return void
     */
    public function setCustomLayout($customLayout)
    {
        $this->customLayout = $customLayout;
    }

    /**
     * @return string|null
     */
    public function getCustomLayoutUpdateXml(): ?string
    {
        return $this->customLayoutUpdateXml;
    }

    /**
     * @param $customLayoutUpdateXml
     * @return void
     */
    public function setCustomLayoutUpdateXml($customLayoutUpdateXml)
    {
        $this->customLayoutUpdateXml = $customLayoutUpdateXml;
    }

    /**
     * @return string|null
     */
    public function getCustomThemeFrom(): ?string
    {
        return $this->customThemeFrom;
    }

    /**
     * @param $customThemeFrom
     * @return void
     */
    public function setCustomThemeFrom($customThemeFrom)
    {
        $this->customThemeFrom = $customThemeFrom;
    }

    /**
     * @return string|null
     */
    public function getCustomThemeTo(): ?string
    {
        return $this->customThemeTo;
    }

    /**
     * @param $customThemeTo
     * @return void
     */
    public function setCustomThemeTo($customThemeTo)
    {
        $this->customThemeTo = $customThemeTo;
    }

    /**
     * @return int|null
     */
    public function getPostsPerPage(): ?int
    {
        return $this->postsPerPage;
    }

    /**
     * @param $postsPerPage
     * @return void
     */
    public function setPostsPerPage($postsPerPage)
    {
        $this->postsPerPage = $postsPerPage;
    }

    /**
     * @return string|null
     */
    public function getPostsListTemplate(): ?string
    {
        return $this->postsListTemplate;
    }

    /**
     * @param $postsListTemplate
     * @return void
     */
    public function setPostsListTemplate($postsListTemplate)
    {
        $this->postsListTemplate = $postsListTemplate;
    }
}
