<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\Category;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class CategoryEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string|null
     */
    protected ?string $title;

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
    protected ?string $contentHeading;

    /**
     * @var string|null
     */
    protected ?string $path;

    /**
     * @var int|null
     */
    protected ?int $position;

    /**
     * @var string|null
     */
    protected ?string $postsSortBy;

    /**
     * @var bool|null
     */
    protected ?bool $includeInMenu;

    /**
     * @var bool|null
     */
    protected ?bool $isActive;

    /**
     * @var int|null
     */
    protected ?int $displayMode;

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
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    /**
     * @param $metaTitle
     * @return void
     */
    public function setMetaTitle($metaTitle)
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
     * @param $metaKeywords
     * @return void
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * @return string|null
     */
    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    /**
     * @param $metaDescription
     * @return void
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @param $identifier
     * @return void
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param $content
     * @return void
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string|null
     */
    public function getContentHeading(): ?string
    {
        return $this->contentHeading;
    }

    /**
     * @param $contentHeading
     * @return void
     */
    public function setContentHeading($contentHeading)
    {
        $this->contentHeading = $contentHeading;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return int|null
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @param $position
     * @return void
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return STRING|null
     */
    public function getPostsSortBy(): ?STRING
    {
        return $this->postsSortBy;
    }

    /**
     * @param $postsSortBy
     * @return void
     */
    public function setPostsSortBy($postsSortBy)
    {
        $this->postsSortBy = $postsSortBy;
    }

    /**
     * @return bool|null
     */
    public function getIncludeInMenu(): ?bool
    {
        return $this->includeInMenu;
    }

    /**
     * @param $includeInMenu
     * @return void
     */
    public function setIncludeInMenu($includeInMenu)
    {
        $this->includeInMenu = $includeInMenu;
    }

    /**
     * @return bool|null
     */
    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    /**
     * @param $isActive
     * @return void
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @return int|null
     */
    public function getDisplayMode(): ?int
    {
        return $this->displayMode;
    }

    /**
     * @param $displayMode
     * @return void
     */
    public function setDisplayMode($displayMode)
    {
        $this->displayMode = $displayMode;
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
