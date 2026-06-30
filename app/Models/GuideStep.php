<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuideStep extends Model
{
    protected $fillable = [
        'title', 'order', 'icon', 'is_active', 'type', 'tip_text',
        'body_text', 'image_path',
        'video_path_basic', 'video_path_plus', 'video_caption',
        'wiring_rows', 'diagram_image_basic', 'diagram_image_plus', 'warning_text',
        'code_content', 'code_language', 'code_note',
        'tool_list_intro',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'wiring_rows' => 'array',
    ];

    public const TYPE_TEXT_IMAGE     = 'text_image';
    public const TYPE_VIDEO          = 'video';
    public const TYPE_WIRING_DIAGRAM = 'wiring_diagram';
    public const TYPE_CODE_BLOCK     = 'code_block';
    public const TYPE_TOOL_LIST      = 'tool_list';

    public static function typeLabels(): array
    {
        return [
            self::TYPE_TEXT_IMAGE     => 'Teks + Gambar',
            self::TYPE_VIDEO          => 'Video Tutorial',
            self::TYPE_WIRING_DIAGRAM => 'Tabel Wiring + Diagram',
            self::TYPE_CODE_BLOCK     => 'Code Block',
            self::TYPE_TOOL_LIST      => 'Daftar Alat (Basic/Plus)',
        ];
    }

    public function toolItems(): HasMany
    {
        return $this->hasMany(ToolItem::class)->orderBy('order');
    }
}
