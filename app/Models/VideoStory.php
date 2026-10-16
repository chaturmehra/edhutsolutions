<?php

namespace App\Models;

use App\Models\Concerns\HasPublishStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoStory extends Model
{
    use HasPublishStatus, SoftDeletes;

    protected $fillable = [
        'name', 'designation', 'quote', 'video_url', 'thumbnail', 'duration', 'sort_order', 'status',
    ];

    public function youtubeId(): ?string
    {
        $pattern = '~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})~';

        return preg_match($pattern, (string) $this->video_url, $match) ? $match[1] : null;
    }

    public function thumbnailUrl(): ?string
    {
        if (filled($this->thumbnail)) {
            return asset($this->thumbnail);
        }

        $id = $this->youtubeId();

        return $id ? 'https://i.ytimg.com/vi/'.$id.'/hqdefault.jpg' : null;
    }
}
