<?php

namespace App\Models;

use App\Helpers\Common\MetaInfo as CommonMetaInfo;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $id
 * @property mixed $created_at
 * @property mixed $created_by
 * @property mixed $updated_at
 * @property mixed $updated_by
 **/

abstract class BaseModel extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    public function setMetaInfo(CommonMetaInfo $meta = null, bool $isCreate = true): void
    {
        if (is_null($meta))
            $meta = new CommonMetaInfo();
        if (isset($this->created_by) && isset($this->updated_by)) {
            if ($isCreate) {
                $this->created_by = $meta->name;
                $this->updated_by = $meta->name;
            } else {
                $this->updated_by = $meta->name;
            }
        }
    }

    public function clearMetaInfo(): void
    {
        $this->created_at = null;
        $this->created_by = null;
        $this->updated_at = null;
        $this->updated_by = null;
    }
}
