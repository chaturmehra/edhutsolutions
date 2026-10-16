<?php

namespace App\Http\Controllers\Learn;

use App\Http\Controllers\Controller;
use App\Models\CoursePdf;
use App\Models\CourseVideo;
use App\Models\MediaAccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaController extends Controller
{
    public function pdf(CoursePdf $pdf): View
    {
        $this->authorizeOwner($pdf);
        abort_unless($pdf->isActive(), 404);
        abort_unless(Storage::disk('local')->exists($pdf->file_path), 404);

        $this->log('pdf', $pdf->id);

        return view('account.courses.pdf', ['pdf' => $pdf, 'owner' => $pdf->owner()]);
    }

    public function pdfFile(Request $request, CoursePdf $pdf): BinaryFileResponse
    {
        $this->authorizeOwner($pdf);
        abort_unless($pdf->isActive(), 404);
        abort_unless(Storage::disk('local')->exists($pdf->file_path), 404);
        abort_unless($this->fromViewer($request), 403, 'Open this PDF from the course page.');

        return $this->stream($pdf->file_path, 'application/pdf');
    }

    public function video(Request $request, CourseVideo $video): BinaryFileResponse
    {
        $this->authorizeOwner($video);
        abort_unless($video->isActive() && $video->source_type === 'upload' && filled($video->file_path), 404);
        abort_unless(Storage::disk('local')->exists($video->file_path), 404);
        abort_unless($request->header('Sec-Fetch-Dest') === 'video', 403, 'Play this video from the course page.');

        if (in_array($request->header('Range'), [null, 'bytes=0-'], true)) {
            $this->log('video', $video->id);
        }

        return $this->stream($video->file_path, 'video/mp4');
    }

    private function authorizeOwner(CoursePdf|CourseVideo $item): void
    {
        $owner = $item->owner();
        abort_if($owner === null, 404);

        $this->authorize('viewContent', $owner);
    }

    private function fromViewer(Request $request): bool
    {
        return $request->header('X-Requested-With') === 'XMLHttpRequest'
            && $request->header('Sec-Fetch-Dest', 'empty') === 'empty';
    }

    private function stream(string $path, string $type): BinaryFileResponse
    {
        $response = response()->file(Storage::disk('local')->path($path), [
            'Content-Type' => $type,
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
            'X-Robots-Tag' => 'noindex, nofollow',
        ]);
        $response->setContentDisposition('inline', 'protected');

        return $response;
    }

    private function log(string $type, int $id): void
    {
        MediaAccessLog::query()->create([
            'user_id' => auth()->id(),
            'resource_type' => $type,
            'resource_id' => $id,
            'ip' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 1000),
        ]);
    }
}
