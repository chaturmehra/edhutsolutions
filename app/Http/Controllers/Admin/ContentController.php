<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumnus;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Client;
use App\Models\CorporateImage;
use App\Models\Course;
use App\Models\Page;
use App\Models\Quiz;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\Trainer;
use App\Models\VideoStory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function index(string $module): View
    {
        $config = $this->config($module);
        $search = request()->string('q')->toString();
        $query = array_key_exists('sort_order', $config['fields'])
            ? $config['model']::query()->orderBy('sort_order')->orderBy('id')
            : $config['model']::query()->latest('id');

        if ($module === 'quizzes') {
            $query->with('course');
        }

        if ($search !== '') {
            $column = $config['search'];
            $query->where($column, 'like', '%'.$search.'%');
        }

        $status = request()->string('status')->toString();
        if (in_array($status, ['active', 'inactive'], true) && array_key_exists('status', $config['fields'])) {
            $query->where('status', $status);
        }

        return view('admin.modules.index', [
            'module' => $module,
            'config' => $config,
            'records' => $query->paginate(10)->withQueryString(),
        ]);
    }

    public function create(string $module): View
    {
        return view('admin.modules.form', [
            'module' => $module,
            'config' => $this->config($module),
            'record' => null,
        ]);
    }

    public function store(Request $request, string $module): RedirectResponse
    {
        $config = $this->config($module);
        if ($this->acceptsManyImages($config) && ! $request->filled('_method')) {
            return $this->storeMany($request, $module, $config);
        }
        $data = $this->validated($request, $config);
        $data = $this->storeUploads($request, $config, $data);
        $seo = $this->pullSeo($data);
        $relations = $this->pullRelations($data, $config);
        $record = $config['model']::query()->create($data);
        $this->saveSeo($record, $seo);
        $this->syncRelations($record, $relations);

        if ($request->input('another') === '1') {
            return redirect()->route('admin.modules.create', $module)->with('status', $config['label'].' saved. Add another.');
        }

        if ($module === 'quizzes' && $record->course_id) {
            return redirect()->route('admin.courses.questions', [$record->course_id, $record->id])
                ->with('status', 'Quiz saved. Add the questions next.');
        }

        return redirect()->route('admin.modules.index', $module)->with('status', $config['label'].' saved.');
    }

    public function edit(string $module, int $id): View
    {
        $config = $this->config($module);
        $query = $config['model']::query();
        if ($this->hasSeo($config)) {
            $query->with('seo');
        }

        return view('admin.modules.form', [
            'module' => $module,
            'config' => $config,
            'record' => $query->findOrFail($id),
        ]);
    }

    public function update(Request $request, string $module, int $id): RedirectResponse
    {
        $config = $this->config($module);
        $record = $config['model']::query()->findOrFail($id);
        $data = $this->validated($request, $config, $record);
        $data = $this->storeUploads($request, $config, $data, $record);
        $seo = $this->pullSeo($data);
        $relations = $this->pullRelations($data, $config);
        $record->update($data);
        $this->saveSeo($record, $seo);
        $this->syncRelations($record, $relations);

        if ($module === 'quizzes' && $record->course_id) {
            return redirect()->route('admin.courses.questions', [$record->course_id, $record->id])
                ->with('status', 'Quiz updated. Review the questions next.');
        }

        return redirect()->route('admin.modules.index', $module)->with('status', $config['label'].' updated.');
    }

    public function destroy(string $module, int $id): RedirectResponse
    {
        $config = $this->config($module);
        $config['model']::query()->findOrFail($id)->delete();

        return back()->with('status', $config['label'].' removed.');
    }

    public function status(string $module, int $id): RedirectResponse
    {
        $config = $this->config($module);
        $record = $config['model']::query()->findOrFail($id);
        if (! array_key_exists('status', $config['fields'])) {
            abort(404);
        }
        $record->update([
            'status' => $record->status === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('status', 'Status updated.');
    }

    /**
     * @return array<string, string>
     */
    public static function navigation(): array
    {
        return [
            'categories' => 'Categories',
            'courses' => 'Courses',
            'quizzes' => 'Quizzes',
            'services' => 'Services',
            'testimonials' => 'Testimonials',
            'trainers' => 'Trainers',
            'hall-of-fame' => 'Hall of fame',
            'video-stories' => 'Video stories',
            'pages' => 'Pages',
            'blogs' => 'Blogs',
            'clients' => 'Clients',
            'corporate-images' => 'Corporate images',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function config(string $module): array
    {
        $modules = [
            'categories' => $this->simple(Category::class, 'Categories', 'name', [
                'name' => $this->text('Name'),
                'slug' => $this->text('Slug', false),
                'description' => $this->area('Description'),
                'status' => $this->statusField(),
            ], ['name' => 'Name', 'slug' => 'Slug', 'status' => 'Status']),
            'courses' => $this->simple(Course::class, 'Courses', 'title', [
                'category_id' => $this->select('Category', fn () => Category::query()->orderBy('name')->pluck('name', 'id')->all()),
                'title' => $this->text('Title'),
                'slug' => $this->text('Slug', false),
                'short_description' => $this->area('Short description'),
                'description' => $this->area('Description', rich: true),
                'overview' => ['label' => 'Course details: Overview tab (leave a blank line between paragraphs)', 'type' => 'textarea', 'rules' => ['nullable', 'string', 'max:10000']],
                'learning_objectives' => ['label' => 'Course details: Learning objectives tab (one objective per line)', 'type' => 'textarea', 'rules' => ['nullable', 'string', 'max:10000']],
                'thumbnail' => $this->fileField('Course image', 'courses'),
                'thumbnail_alt' => $this->text('Image alt text', false),
                'duration' => $this->text('Duration', false),
                'level' => $this->text('Level', false),
                'price' => ['label' => 'Price', 'type' => 'number', 'rules' => ['nullable', 'numeric', 'min:0']],
                'price_on_request' => ['label' => 'Price on request', 'type' => 'checkbox', 'rules' => ['nullable', 'boolean']],
                'is_featured' => ['label' => 'Featured', 'type' => 'checkbox', 'rules' => ['nullable', 'boolean']],
                'packages_note' => ['label' => 'Package footnote (shown under the package cards)', 'type' => 'textarea', 'rules' => ['nullable', 'string', 'max:1000']],
                'status' => $this->statusField(),
                ...$this->seoFields(),
            ], ['title' => 'Title', 'status' => 'Status']),
            'quizzes' => $this->simple(Quiz::class, 'Quizzes', 'title', [
                'course_id' => [
                    'label' => 'Course',
                    'type' => 'select',
                    'options' => fn () => Course::query()->orderBy('title')->pluck('title', 'id')->all(),
                    'rules' => ['required', 'integer', Rule::exists('courses', 'id')],
                ],
                'title' => $this->text('Title'),
                'slug' => $this->text('Slug', false),
                'description' => $this->area('Description'),
                'kind' => [
                    'label' => 'Test type',
                    'type' => 'select',
                    'options' => Quiz::KINDS,
                    'rules' => ['required', Rule::in(array_keys(Quiz::KINDS))],
                ],
                'pass_percentage' => ['label' => 'Pass percentage', 'type' => 'number', 'rules' => ['required', 'numeric', 'min:0', 'max:100']],
                'negative_marking' => ['label' => 'Negative marking', 'type' => 'number', 'rules' => ['nullable', 'numeric', 'min:0']],
                'time_limit_minutes' => ['label' => 'Time limit (minutes)', 'type' => 'number', 'rules' => ['nullable', 'integer', 'min:1']],
                'questions_per_attempt' => ['label' => 'Questions per attempt (mock tests draw these at random)', 'type' => 'number', 'rules' => ['nullable', 'integer', 'min:1', 'max:1000']],
                'status' => $this->statusField(),
            ], ['title' => 'Title', 'course.title' => 'Course', 'kind' => 'Type', 'status' => 'Status']),
            'services' => $this->simple(Service::class, 'Services', 'title', [
                'title' => $this->text('Title'),
                'slug' => $this->text('Slug', false),
                'short_description' => $this->area('Short description'),
                'description' => $this->area('Description', rich: true),
                'status' => $this->statusField(),
                ...$this->seoFields(),
            ], ['title' => 'Title', 'status' => 'Status']),
            'testimonials' => $this->simple(Testimonial::class, 'Testimonials', 'name', [
                'course_id' => [
                    'label' => 'Course (shows this review on that course page)',
                    'type' => 'select',
                    'options' => fn () => ['' => 'General review (no course)'] + Course::query()->orderBy('title')->pluck('title', 'id')->all(),
                    'rules' => ['nullable', 'integer', Rule::exists('courses', 'id')],
                ],
                'name' => $this->text('Name'),
                'headline' => $this->text('Headline', false),
                'quote' => $this->area('Quote', true),
                'rating' => ['label' => 'Rating', 'type' => 'number', 'rules' => ['nullable', 'numeric', 'min:1', 'max:5']],
                'status' => $this->statusField(),
            ], ['name' => 'Name', 'course.title' => 'Course', 'headline' => 'Headline', 'status' => 'Status']),
            'trainers' => $this->simple(Trainer::class, 'Trainers', 'name', [
                'name' => $this->text('Name'),
                'image' => $this->fileField('Photo (portrait works best)', 'trainers'),
                'credentials' => $this->text('Credentials, e.g. PMP, PgMP, PfMP', false),
                'experience' => ['label' => 'Experience, e.g. 25+ Years Experience', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:100']],
                'bio' => $this->area('Short bio'),
                'linkedin_url' => ['label' => 'LinkedIn profile URL', 'type' => 'text', 'rules' => ['nullable', 'url', 'max:500']],
                'courses' => [
                    'label' => 'Courses they teach (shows the trainer on those course pages)',
                    'type' => 'checklist',
                    'relation' => 'courses',
                    'options' => fn () => Course::query()->orderBy('title')->pluck('title', 'id')->all(),
                    'rules' => ['nullable', 'array'],
                    'item_rules' => ['integer', Rule::exists('courses', 'id')],
                ],
                'sort_order' => ['label' => 'Sort order', 'type' => 'number', 'rules' => ['nullable', 'integer', 'min:0']],
                'status' => $this->statusField(),
            ], ['name' => 'Name', 'image' => 'Photo', 'credentials' => 'Credentials', 'sort_order' => 'Order', 'status' => 'Status']),
            'hall-of-fame' => $this->simple(Alumnus::class, 'Hall of fame', 'name', [
                'course_id' => [
                    'label' => 'Certification earned (shows them on that course page)',
                    'type' => 'select',
                    'options' => fn () => Course::query()->orderBy('title')->pluck('title', 'id')->all(),
                    'rules' => ['required', 'integer', Rule::exists('courses', 'id')],
                ],
                'name' => $this->text('Name'),
                'image' => $this->fileField('Photo (portrait works best)', 'alumni'),
                'job_title' => ['label' => 'Job title, e.g. Senior Project Manager', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:150']],
                'sort_order' => ['label' => 'Sort order', 'type' => 'number', 'rules' => ['nullable', 'integer', 'min:0']],
                'status' => $this->statusField(),
            ], ['name' => 'Name', 'image' => 'Photo', 'course.title' => 'Certification', 'sort_order' => 'Order', 'status' => 'Status']),
            'video-stories' => $this->simple(VideoStory::class, 'Video stories', 'name', [
                'name' => $this->text('Name'),
                'designation' => $this->text('Role and company', false),
                'quote' => $this->area('Short quote'),
                'video_url' => ['label' => 'Video link (YouTube or other URL)', 'type' => 'text', 'rules' => ['required', 'url', 'max:500']],
                'thumbnail' => $this->fileField('Thumbnail (optional for YouTube)', 'video-stories'),
                'duration' => ['label' => 'Duration, e.g. 2:07', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:20']],
                'sort_order' => ['label' => 'Sort order', 'type' => 'number', 'rules' => ['nullable', 'integer', 'min:0']],
                'status' => $this->statusField(),
            ], ['name' => 'Name', 'thumbnail' => 'Thumbnail', 'sort_order' => 'Order', 'status' => 'Status']),
            'pages' => $this->simple(Page::class, 'Pages', 'title', [
                'title' => $this->text('Title'),
                'slug' => $this->text('Slug', false),
                'excerpt' => $this->area('Excerpt'),
                'content' => $this->area('Content', rich: true),
                'status' => $this->statusField(),
                ...$this->seoFields(),
            ], ['title' => 'Title', 'slug' => 'Slug', 'status' => 'Status']),
            'clients' => $this->simple(Client::class, 'Clients', 'name', [
                'name' => $this->text('Name'),
                'logo' => $this->fileField('Logo', 'clients'),
                'sort_order' => ['label' => 'Sort order', 'type' => 'number', 'rules' => ['nullable', 'integer', 'min:0']],
                'status' => $this->statusField(),
            ], ['name' => 'Name', 'logo' => 'Logo', 'sort_order' => 'Order', 'status' => 'Status']),
            'corporate-images' => $this->simple(CorporateImage::class, 'Corporate images', 'title', [
                'images' => ['label' => 'Images', 'type' => 'files', 'only' => 'create', 'rules' => ['nullable'], 'folder' => 'corporate'],
                'title' => $this->text('Title', false),
                'alt_text' => $this->text('Alt text', false),
                'image' => $this->fileField('Replace image', 'corporate', 'edit'),
                'sort_order' => ['label' => 'Sort order', 'type' => 'number', 'rules' => ['nullable', 'integer', 'min:0']],
                'status' => $this->statusField(),
            ], ['title' => 'Title', 'image' => 'Image', 'sort_order' => 'Order', 'status' => 'Status']),
            'blogs' => $this->simple(Blog::class, 'Blogs', 'title', [
                'title' => $this->text('Title'),
                'slug' => $this->text('Slug', false),
                'category' => [
                    'label' => 'Category',
                    'type' => 'select',
                    'options' => Blog::CATEGORIES,
                    'rules' => ['required', 'string', Rule::in(array_keys(Blog::CATEGORIES))],
                ],
                'excerpt' => $this->area('Excerpt'),
                'content' => $this->area('Content', true, true),
                'image' => $this->text('Image path', false),
                'published_on' => ['label' => 'Published on', 'type' => 'date', 'rules' => ['nullable', 'date']],
                'status' => $this->statusField(),
                ...$this->seoFields(),
            ], ['title' => 'Title', 'category' => 'Category', 'published_on' => 'Published', 'status' => 'Status']),
        ];

        abort_unless(isset($modules[$module]), 404);

        return $modules[$module];
    }

    /**
     * @param  array<string, mixed>  $fields
     * @param  array<string, string>  $columns
     * @return array<string, mixed>
     */
    private function simple(string $model, string $label, string $search, array $fields, array $columns): array
    {
        return compact('model', 'label', 'search', 'fields', 'columns');
    }

    /**
     * @return array<string, mixed>
     */
    private function text(string $label, bool $required = true): array
    {
        return [
            'label' => $label,
            'type' => 'text',
            'rules' => [$required ? 'required' : 'nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function area(string $label, bool $required = false, bool $rich = false): array
    {
        return [
            'label' => $label,
            'type' => 'textarea',
            'rich' => $rich,
            'rules' => [$required ? 'required' : 'nullable', 'string'],
        ];
    }

    /**
     * @param  callable(): array<int|string, string>  $options
     * @return array<string, mixed>
     */
    private function select(string $label, callable $options): array
    {
        return [
            'label' => $label,
            'type' => 'select',
            'options' => $options,
            'rules' => ['required', 'integer'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function statusField(): array
    {
        return [
            'label' => 'Status',
            'type' => 'select',
            'options' => ['active' => 'Active', 'inactive' => 'Inactive'],
            'rules' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function seoFields(): array
    {
        return [
            'meta_title' => ['label' => 'Meta title', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255'], 'group' => 'seo'],
            'meta_keywords' => ['label' => 'Meta keywords', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255'], 'group' => 'seo'],
            'meta_description' => ['label' => 'Meta description', 'type' => 'textarea', 'rules' => ['nullable', 'string', 'max:320'], 'group' => 'seo'],
        ];
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function hasSeo(array $config): bool
    {
        return collect($config['fields'])->contains(fn ($field) => ($field['group'] ?? '') === 'seo');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function pullSeo(array &$data): array
    {
        $seo = [];
        foreach (['meta_title', 'meta_keywords', 'meta_description'] as $key) {
            if (array_key_exists($key, $data)) {
                $seo[$key] = $data[$key] !== '' ? $data[$key] : null;
                unset($data[$key]);
            }
        }

        return $seo;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $config
     * @return array<string, array<int, int>>
     */
    private function pullRelations(array &$data, array $config): array
    {
        $relations = [];
        foreach ($config['fields'] as $name => $field) {
            if (($field['type'] ?? '') === 'checklist') {
                $relations[$field['relation']] = array_map('intval', (array) ($data[$name] ?? []));
                unset($data[$name]);
            }
        }

        return $relations;
    }

    /**
     * @param  array<string, array<int, int>>  $relations
     */
    private function syncRelations(Model $record, array $relations): void
    {
        foreach ($relations as $relation => $ids) {
            $record->{$relation}()->sync($ids);
        }
    }

    /**
     * @param  array<string, mixed>  $seo
     */
    private function saveSeo(Model $record, array $seo): void
    {
        if ($seo === [] || ! method_exists($record, 'seo')) {
            return;
        }

        if (collect($seo)->filter(fn ($value) => filled($value))->isEmpty()) {
            $record->seo()?->delete();

            return;
        }

        $record->seo()->updateOrCreate([], $seo);
    }

    /**
     * @return array<string, mixed>
     */
    private function fileField(string $label, string $folder, ?string $only = null): array
    {
        return [
            'label' => $label,
            'type' => 'file',
            'rules' => ['nullable', 'image', 'max:5120'],
            'folder' => $folder,
            'only' => $only,
        ];
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function acceptsManyImages(array $config): bool
    {
        return collect($config['fields'])->contains(fn ($field) => ($field['type'] ?? '') === 'files');
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function storeMany(Request $request, string $module, array $config): RedirectResponse
    {
        $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'max:5120'],
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $sort = (int) ($request->input('sort_order') ?: 0);
        foreach ($request->file('images', []) as $index => $file) {
            $title = $request->input('title') ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $config['model']::query()->create([
                'title' => $title,
                'alt_text' => $request->input('alt_text') ?: $title,
                'image' => $this->storePublicImage($file, 'corporate'),
                'sort_order' => $sort + $index,
                'status' => $request->input('status'),
            ]);
        }

        return redirect()->route('admin.modules.index', $module)->with('status', 'Images saved.');
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function storeUploads(Request $request, array $config, array $data, ?Model $record = null): array
    {
        foreach ($config['fields'] as $name => $field) {
            if (($field['type'] ?? '') !== 'file') {
                continue;
            }
            if ($request->hasFile($name)) {
                $data[$name] = $this->storePublicImage($request->file($name), $field['folder'] ?? 'uploads');
            } else {
                unset($data[$name]);
            }
        }

        if ($record && array_key_exists('logo', $config['fields']) && empty($record->logo) && empty($data['logo'])) {
            abort(422, 'A logo is required.');
        }

        return $data;
    }

    private function storePublicImage(UploadedFile $file, string $folder): string
    {
        $directory = public_path('assets/images/'.$folder);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        $name = Str::uuid()->toString().'.'.strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $file->move($directory, $name);

        return 'assets/images/'.$folder.'/'.$name;
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array<string, mixed>
     */
    private function validated(Request $request, array $config, ?Model $record = null): array
    {
        foreach (['time_limit_minutes', 'questions_per_attempt', 'negative_marking', 'pass_percentage', 'price', 'rating', 'sort_order'] as $numeric) {
            if ($request->exists($numeric) && $request->input($numeric) === '') {
                $request->merge([$numeric => null]);
            }
        }

        $rules = [];
        foreach ($config['fields'] as $name => $field) {
            if (in_array($field['type'] ?? '', ['file', 'files'], true)) {
                if (($field['type'] ?? '') === 'file') {
                    $rules[$name] = [$record || $name !== 'logo' ? 'nullable' : 'required', 'image', 'max:5120'];
                }
                continue;
            }
            $rules[$name] = $field['rules'];
            if (isset($field['item_rules'])) {
                $rules[$name.'.*'] = $field['item_rules'];
            }
        }
        $data = $request->validate($rules);

        foreach ($config['fields'] as $name => $field) {
            if (($field['type'] ?? '') === 'checkbox') {
                $data[$name] = $request->boolean($name);
            }
            if (($field['type'] ?? '') === 'select' && is_callable($field['options'] ?? null)) {
                $field['options'] = $field['options']();
            }
        }

        foreach (['slug'] as $slugField) {
            if (array_key_exists($slugField, $config['fields']) && empty($data[$slugField])) {
                $source = $data['title'] ?? $data['name'] ?? 'item';
                $data[$slugField] = Str::slug($source);
            }
        }

        if (array_key_exists('price', $data) && $data['price'] === null) {
            $data['price'] = 0;
        }
        if (array_key_exists('rating', $data) && ($data['rating'] === null || $data['rating'] === '')) {
            $data['rating'] = 5;
        }
        if (array_key_exists('sort_order', $data) && ($data['sort_order'] === null || $data['sort_order'] === '')) {
            $data['sort_order'] = 0;
        }
        if (array_key_exists('negative_marking', $data) && $data['negative_marking'] === null) {
            $data['negative_marking'] = 0;
        }

        return $data;
    }
}
