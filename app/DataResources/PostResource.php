<?php

namespace App\DataResources;

use App\Helpers\Common\CommonHelper;
use App\Models\Post;

class PostResource extends BaseDataResource
{

    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $content;

    /**
     * @var int
     */
    protected $user_id;

    /**
     * @var int
     */
    public $likes;

    /**
     * @var int
     */
    public $views;

    /**
     * @var int
     */
    public $comment_total;

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
    public $tag_name;

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
        return Post::class;
    }

    /**
     * @var array|string[]
     */
    protected array $fields = [
        'id',
        'title',
        'content',
        'user_id',
        'likes',
        'views',
        'comments',
        'comment_total',
        'user_name',
        'user_avatar_url',
        'user',
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

        $this->tag_name = '@' . strtolower($this->user_name);

        $this->comment_total = count($object->comments);
    }
}
