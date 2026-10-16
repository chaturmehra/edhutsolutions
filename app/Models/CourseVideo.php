<?php

namespace App\Models;

use App\Models\Concerns\HasPublishStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseVideo extends Model
{
    use HasPublishStatus, SoftDeletes;

    protected $fillable = [
        'course_id', 'service_id', 'lesson_id', 'title', 'source_type', 'file_path',
        'external_url', 'duration', 'sort_order', 'status',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * The course or service this video belongs to; access to it follows that owner's enrollment.
     */
    public function owner(): Course|Service|null
    {
        return $this->service_id ? $this->service : $this->course;
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * How the video plays inside the course: a private upload, a direct video file, or a provider's
     * embeddable player. Null means the link cannot be embedded and has to open in a new tab.
     *
     * @return array{type: 'file'|'frame', src: string, protected: bool}|null
     */
    public function player(): ?array
    {
        if ($this->source_type === 'upload') {
            return filled($this->file_path)
                ? ['type' => 'file', 'src' => route('learn.video', $this), 'protected' => true]
                : null;
        }

        $url = trim((string) $this->external_url);
        if (! preg_match('~^https?://~i', $url)) {
            return null;
        }

        $frame = fn (string $src) => ['type' => 'frame', 'src' => $src, 'protected' => false];

        if (preg_match('~(?:youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})~i', $url, $match)) {
            return $frame('https://www.youtube-nocookie.com/embed/'.$match[1].'?rel=0&modestbranding=1&autoplay=1');
        }
        if (preg_match('~vimeo\.com/(?:video/)?(\d+)(?:/([0-9a-f]+))?~i', $url, $match)) {
            parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
            $hash = $match[2] ?? ($query['h'] ?? null);

            return $frame('https://player.vimeo.com/video/'.$match[1].'?autoplay=1'.($hash ? '&h='.rawurlencode($hash) : ''));
        }
        if (preg_match('~drive\.google\.com/(?:file/d/|open\?(?:.*&)?id=)([A-Za-z0-9_-]{10,})~i', $url, $match)) {
            return $frame('https://drive.google.com/file/d/'.$match[1].'/preview');
        }
        if (preg_match('~loom\.com/(?:share|embed)/([0-9a-f]{20,})~i', $url, $match)) {
            return $frame('https://www.loom.com/embed/'.$match[1].'?autoplay=1');
        }
        if (preg_match('~\.(mp4|webm|ogv|ogg|m4v)$~i', (string) parse_url($url, PHP_URL_PATH))) {
            return ['type' => 'file', 'src' => $url, 'protected' => false];
        }

        return null;
    }
}
