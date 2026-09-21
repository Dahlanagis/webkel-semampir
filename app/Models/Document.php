<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    public function files()
    {
        return $this->hasMany(DocumentFile::class);
    }

    protected static function booted()
    {
        static::deleting(function ($document) {
            foreach ($document->files as $file) {
                if ($file->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($file->file_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($file->file_path);
                }
            }
        });
    }
}
