<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'name',
        'file_path',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    protected static function booted()
    {
        static::deleting(function ($file) {
            if ($file->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($file->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($file->file_path);
            }
        });
    }
}
