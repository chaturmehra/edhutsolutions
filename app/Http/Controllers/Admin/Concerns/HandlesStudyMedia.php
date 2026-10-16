<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\CourseVideo;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

trait HandlesStudyMedia
{
    /**
     * @return array{title: string, description: string|null, sort_order: int, status: string}
     */
    private function pdfData(Request $request, bool $creating): array
    {
        $this->blankToNull($request, ['sort_order']);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'file' => [$creating ? 'required' : 'nullable', 'file', 'mimes:pdf', 'max:20480'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        return [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'status' => $data['status'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function storedPdf(UploadedFile $file, string $directory): array
    {
        return [
            'file_path' => $this->storePrivate($file, $directory, 'pdf'),
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize() ?: 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function videoPayload(Request $request, ?CourseVideo $video, string $directory): array
    {
        $this->blankToNull($request, ['duration', 'external_url', 'sort_order']);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'source_type' => ['required', Rule::in(['upload', 'link'])],
            'file' => ['nullable', 'file', 'mimes:mp4', 'max:102400'],
            'external_url' => ['nullable', 'url', 'max:1000'],
            'duration' => ['nullable', 'string', 'max:40'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $errors = [];
        if ($data['source_type'] === 'upload' && ! $request->hasFile('file') && ! filled($video?->file_path)) {
            $errors['file'] = 'Choose an MP4 video to upload.';
        }
        if ($data['source_type'] === 'link' && ! filled($data['external_url'] ?? null)) {
            $errors['external_url'] = 'Add the video link.';
        }
        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        $payload = [
            'title' => $data['title'],
            'source_type' => $data['source_type'],
            'duration' => $data['duration'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'status' => $data['status'],
        ];

        if ($data['source_type'] === 'link') {
            if (filled($video?->file_path)) {
                $this->deletePrivate($video->file_path);
            }
            $payload['file_path'] = null;
            $payload['external_url'] = $data['external_url'];
        } else {
            $payload['external_url'] = null;
            if ($request->hasFile('file')) {
                if (filled($video?->file_path)) {
                    $this->deletePrivate($video->file_path);
                }
                $payload['file_path'] = $this->storePrivate($request->file('file'), $directory, 'mp4');
            }
        }

        return $payload;
    }

    private function storePrivate(UploadedFile $file, string $directory, string $extension): string
    {
        return $file->storeAs($directory, Str::uuid()->toString().'.'.$extension, 'local');
    }

    private function deletePrivate(?string $path): void
    {
        if (! $path || str_contains($path, '..') || ! Str::startsWith($path, ['courses/', 'services/'])) {
            return;
        }
        Storage::disk('local')->delete($path);
    }

    /**
     * @param  array<int, string>  $keys
     */
    private function blankToNull(Request $request, array $keys): void
    {
        $merge = [];
        foreach ($keys as $key) {
            if ($request->exists($key) && $request->input($key) === '') {
                $merge[$key] = null;
            }
        }
        if ($merge !== []) {
            $request->merge($merge);
        }
    }
}
