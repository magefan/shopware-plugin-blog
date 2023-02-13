<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\Comment;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class CommentEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string|null
     */
    protected ?string $parentId;

    /**
     * @var string|null
     */
    protected ?string $customerId;

    /**
     * @var string|null
     */
    protected ?string $adminId;

    /**
     * @var int
     */
    protected int $status;

    /**
     * @var int
     */
    protected int $authorType;

    /**
     * @var string
     */
    protected string $authorNickname;

    /**
     * @var string|null
     */
    protected ?string $authorEmail;

    /**
     * @var string|null
     */
    protected ?string $text;

    /**
     * @var string
     */
    protected string $postId;

    /**
     * @return string|null
     */
    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    /**
     * @param string|null $parentId
     */
    public function setParentId(?string $parentId): void
    {
        $this->parentId = $parentId;
    }

    /**
     * @return string|null
     */
    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    /**
     * @param string|null $customerId
     */
    public function setCustomerId(?string $customerId): void
    {
        $this->customerId = $customerId;
    }

    /**
     * @return string|null
     */
    public function getAdminId(): ?string
    {
        return $this->adminId;
    }

    /**
     * @param string|null $adminId
     */
    public function setAdminId(?string $adminId): void
    {
        $this->adminId = $adminId;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param $status
     * @return void
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getAuthorType(): int
    {
        return $this->authorType;
    }

    /**
     * @param int $authorType
     */
    public function setAuthorType(int $authorType): void
    {
        $this->authorType = $authorType;
    }

    /**
     * @return string
     */
    public function getAuthorNickname(): string
    {
        return $this->authorNickname;
    }

    /**
     * @param string $authorNickname
     */
    public function setAuthorNickname(string $authorNickname): void
    {
        $this->authorNickname = $authorNickname;
    }

    /**
     * @return string|null
     */
    public function getAuthorEmail(): ?string
    {
        return $this->authorEmail;
    }

    /**
     * @param string|null $authorEmail
     */
    public function setAuthorEmail(?string $authorEmail): void
    {
        $this->authorEmail = $authorEmail;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     */
    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getPostId(): string
    {
        return $this->postId;
    }

    /**
     * @param string $postId
     */
    public function setPostId(string $postId): void
    {
        $this->postId = $postId;
    }
}
