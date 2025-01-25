<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    use CrudTrait;
    protected $fillable = ['name', 'video'];

    public function setVideoAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['video'] = $value;
        } else {
            $this->attributes['video'] = $value->store('videos/actors', 'public');
        }
    }

    public function videoQueues()
    {
        return $this->hasMany(VideoQueue::class);
    }
}
