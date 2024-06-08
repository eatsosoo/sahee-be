<?php

namespace App\DataResources;

use App\Helpers\Common\CommonHelper;
use App\Models\Comment;

class CommentResource extends BaseDataResource
{

    /**
     * @var string
     */
    public $content;

    /**
     * @var int
     */
    protected $parent_id;

    /**
     * @var int
     */
    public $user_id;

    /**
     * @var int
     */
    public $book_id;

    /**
     * @var string
     */
    public $user_name;

    /**
     * @var string
     */
    public $user_avatar_url;

    /**
     * @var string
     */
    public $created_at;

    /**
     * @var string
     */
    public $updated_at;

    public function modelClass(): string
    {
        return Comment::class;
    }

    /**
     * @var array|string[]
     */
    protected array $fields = [
        'id',
        'content',
        'user_id',
        'book_id',
        'parent_id',
        'user_name',
        'user_avatar_url',
        'created_at',
        'updated_at',
    ];

    public function load(mixed $object): void
    {
        parent::copy($object, $this->fields);
        $this->created_at = CommonHelper::formatDate($object->created_at);
        $this->updated_at = CommonHelper::formatDate($object->updated_at);

        $this->user_name = $object->user->name;
        $this->user_avatar_url = $object->user->avatar_url;
    }
}
