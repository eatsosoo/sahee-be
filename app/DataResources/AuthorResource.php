<?php

namespace App\DataResources;

use App\Helpers\Common\CommonHelper;
use App\Models\Author;

class AuthorResource extends BaseDataResource
{

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $nationality;

    /**
     * @var string
     */
    public $dob;

    /**
     * @var string
     */
    public $pseudonym;

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
        return Author::class;
    }

    /**
     * @var array|string[]
     */
    protected array $fields = [
        'id',
        'name',
        'nationality',
        'dob',
        'pseudonym',
    ];

    public function load(mixed $object): void
    {
        parent::copy($object, $this->fields);
        $this->created_at = CommonHelper::formatDate($object->created_at);
        $this->updated_at = CommonHelper::formatDate($object->updated_at);
    }
}
