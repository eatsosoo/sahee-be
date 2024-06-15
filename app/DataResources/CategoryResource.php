<?php

namespace App\DataResources;

use App\Helpers\Common\CommonHelper;
use App\Models\Category;

class CategoryResource extends BaseDataResource
{

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $image_url;

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
        return Category::class;
    }

    /**
     * @var array|string[]
     */
    protected array $fields = [
        'id',
        'name',
        'image_url',
    ];

    public function load(mixed $object): void
    {
        parent::copy($object, $this->fields);
        $this->created_at = CommonHelper::formatDate($object->created_at);
        $this->updated_at = CommonHelper::formatDate($object->updated_at);
    }
}
