<?php

namespace App\DataResources;

use App\Helpers\Common\CommonHelper;
use App\Models\Book;

class BookResource extends BaseDataResource
{

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

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
    public $category_id;

    /**
     * @var string
     */
    public $category_name;

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
        return Book::class;
    }

    /**
     * @var array|string[]
     */
    protected array $fields = [
        'id',
        'name',
        'author',
        'description',
        'user_id',
        'price',
        'stock',
        'book_cover_url',
        'comments',
        'comment_total',
        'category_id',
        'category_name',
        'created_at',
        'updated_at',
    ];

    public function load(mixed $object): void
    {
        parent::copy($object, $this->fields);
        $this->created_at = CommonHelper::formatDate($object->created_at);
        $this->updated_at = CommonHelper::formatDate($object->updated_at);

        $this->category_name = $object->category->name;

        $this->comment_total = count($object->comments);
    }
}
