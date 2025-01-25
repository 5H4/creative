<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoQueue extends Model
{
    use CrudTrait;
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'video_queues';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    protected $fillable = [
        'actor_id', 'locales_id', 'voices_id', 'state', 'rate_change', 'pitch_change', 'volume_change', 
        'prosody_contour', 'text', 'status', 'video_url', 'video_local_path', 
        'voice_local_path', 'process_time_start', 'process_time_end', 
        'guidance_scale', 'inference_steps', 'seed'
    ];

    /*
    |---------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function actor()
    {
        return $this->belongsTo(Actor::class);
    }

    public function locales()
    {
        return $this->belongsTo(Locales::class);
    }

    public function voices()
    {
        return $this->belongsTo(Voices::class);
    }

    public function countries()
    {
        return $this->belongsTo(Countries::class, 'state');
    }

    // public function localeVoice()
    // {
    //     return $this->belongsTo(LocaleVoice::class);
    // }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
