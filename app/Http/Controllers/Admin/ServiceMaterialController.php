<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesStudyMedia;
use App\Http\Controllers\Controller;
use App\Models\CoursePdf;
use App\Models\CourseVideo;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceMaterialController extends Controller
{
    use HandlesStudyMedia;

    private const BASE = 'admin.services';

    public function show(int $service): View
    {
        $service = $this->service($service);
        $service->load(['pdfs', 'videos'])->loadCount(['enrollments' => fn ($query) => $query->where('status', 'active')]);

        return view('admin.services.materials', ['service' => $service]);
    }

    public function createPdf(int $service): View
    {
        $service = $this->service($service);

        return view('admin.courses.pdf-form', [
            'base' => self::BASE,
            'owner' => $service,
            'pdf' => null,
            'nextOrder' => (int) $service->pdfs()->max('sort_order') + 1,
        ]);
    }

    public function storePdf(Request $request, int $service): RedirectResponse
    {
        $service = $this->service($service);
        $data = $this->pdfData($request, true);
        $service->pdfs()->create([...$data, ...$this->storedPdf($request->file('file'), 'services/pdfs')]);

        return redirect()->route('admin.services.materials', $service->id)->with('status', 'PDF uploaded.');
    }

    public function editPdf(int $service, int $pdf): View
    {
        $service = $this->service($service);

        return view('admin.courses.pdf-form', [
            'base' => self::BASE,
            'owner' => $service,
            'pdf' => $this->pdf($service, $pdf),
            'nextOrder' => 1,
        ]);
    }

    public function updatePdf(Request $request, int $service, int $pdf): RedirectResponse
    {
        $service = $this->service($service);
        $pdf = $this->pdf($service, $pdf);
        $data = $this->pdfData($request, false);
        if ($request->hasFile('file')) {
            $this->deletePrivate($pdf->file_path);
            $data = [...$data, ...$this->storedPdf($request->file('file'), 'services/pdfs')];
        }
        $pdf->update($data);

        return redirect()->route('admin.services.materials', $service->id)->with('status', 'PDF updated.');
    }

    public function destroyPdf(int $service, int $pdf): RedirectResponse
    {
        $service = $this->service($service);
        $pdf = $this->pdf($service, $pdf);
        $this->deletePrivate($pdf->file_path);
        $pdf->delete();

        return redirect()->route('admin.services.materials', $service->id)->with('status', 'PDF removed.');
    }

    public function createVideo(int $service): View
    {
        $service = $this->service($service);

        return view('admin.courses.video-form', [
            'base' => self::BASE,
            'owner' => $service,
            'video' => null,
            'nextOrder' => (int) $service->videos()->max('sort_order') + 1,
        ]);
    }

    public function storeVideo(Request $request, int $service): RedirectResponse
    {
        $service = $this->service($service);
        $service->videos()->create($this->videoPayload($request, null, 'services/videos'));

        return redirect()->route('admin.services.materials', $service->id)->with('status', 'Video saved.');
    }

    public function editVideo(int $service, int $video): View
    {
        $service = $this->service($service);

        return view('admin.courses.video-form', [
            'base' => self::BASE,
            'owner' => $service,
            'video' => $this->video($service, $video),
            'nextOrder' => 1,
        ]);
    }

    public function updateVideo(Request $request, int $service, int $video): RedirectResponse
    {
        $service = $this->service($service);
        $video = $this->video($service, $video);
        $video->update($this->videoPayload($request, $video, 'services/videos'));

        return redirect()->route('admin.services.materials', $service->id)->with('status', 'Video updated.');
    }

    public function destroyVideo(int $service, int $video): RedirectResponse
    {
        $service = $this->service($service);
        $video = $this->video($service, $video);
        $this->deletePrivate($video->file_path);
        $video->delete();

        return redirect()->route('admin.services.materials', $service->id)->with('status', 'Video removed.');
    }

    private function service(int $service): Service
    {
        return Service::query()->findOrFail($service);
    }

    private function pdf(Service $service, int $pdf): CoursePdf
    {
        return $service->pdfs()->findOrFail($pdf);
    }

    private function video(Service $service, int $video): CourseVideo
    {
        return $service->videos()->findOrFail($video);
    }
}
