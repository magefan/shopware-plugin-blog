<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\PostTag;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class PostTagEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string|null
     */
    protected ?string $postId;

    /**
     * @var string|null
     */
    protected ?string $tagId;

    /**
     * @return string|null
     */
    public function getPostId(): ?string
    {
        return $this->postId;
    }

    /**
     * @param string|null $postId
     * @return void
     */
    public function setPostId(?string $postId): void
    {
        $this->postId = $postId;
    }

    /**
     * @return string|null
     */
    public function getTagId(): ?string
    {
        return $this->tagId;
    }

    /**
     * @param string|null $tagId
     * @return void
     */
    public function setTagId(?string $tagId): void
    {
        $this->tagId = $tagId;
    }
}
