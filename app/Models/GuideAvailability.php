<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuideAvailability extends Model
{
    protected $fillable = ['guide_id', 'day_of_week', 'start_time', 'end_time', 'is_available'];
    protected $casts    = ['is_available' => 'boolean'];

    public function guide() { return $this->belongsTo(Guide::class); }
}
