<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToolItem extends Model
{
    protected $fillable = ['guide_step_id', 'name', 'description', 'icon', 'package', 'order'];

    public function guideStep(): BelongsTo
    {
        return $this->belongsTo(GuideStep::class);
    }
}