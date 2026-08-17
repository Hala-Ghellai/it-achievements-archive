<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\AchievementMedia;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AchievementController extends Controller
{
    /**
     * Show a single achievement with its full media, employees and date/time.
     */
    public function show(Achievement $achievement): View
    {
        $achievement->load(['department', 'employees', 'media']);

        return view('achievements.show', [
            'achievement' => $achievement,
        ]);
    }

    /**
     * Show the form to create a new achievement (authorized users only).
     */
    public function create(Request $request): View
    {
        Gate::authorize('create-achievements');

        $user = $request->user();
        $preselected = null;

        if ($request->query('department')) {
            $preselected = Department::where('slug', $request->query('department'))->firstOrFail();
        }

        if (! $user->isAdmin() && $user->department_id !== null) {
            $preselected = $user->department;
        }

        $departments = Department::orderBy('sort_order')->get();

        return view('achievements.create', [
            'departments' => $departments,
            'preselected' => $preselected,
        ]);
    }

    /**
     * Store a newly created achievement with optional thumbnail, images, videos, PDFs.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create-achievements');

        $data = $request->validate($this->rules(), $this->messages());

        $department = Department::findOrFail($data['department_id']);

        if (! Gate::allows('create-achievement-in', $department)) {
            return back()
                ->withInput()
                ->withErrors(['department_id' => 'غير مسموح بإضافة إنجازات إلى هذا القسم.']);
        }

        $achievement = Achievement::create([
            'department_id' => $department->id,
            'title' => $data['title'],
            'description' => $data['description'],
            'achievement_at' => $data['achievement_at'],
            'created_by' => $request->user()->id,
        ]);

        $sortOrder = 0;

        if ($request->hasFile('thumbnail')) {
            $this->storeMedia($request->file('thumbnail'), $achievement, AchievementMedia::TYPE_IMAGE, $sortOrder++);
        }

        foreach (['images' => AchievementMedia::TYPE_IMAGE, 'videos' => AchievementMedia::TYPE_VIDEO, 'pdfs' => AchievementMedia::TYPE_PDF] as $field => $type) {
            foreach ($request->file($field, []) as $file) {
                $this->storeMedia($file, $achievement, $type, $sortOrder++);
            }
        }

        if (! empty($data['employees'])) {
            foreach (preg_split('/[\r\n,،]+/u', $data['employees']) as $name) {
                $name = trim($name);
                if ($name === '') {
                    continue;
                }
                $employee = Employee::firstOrCreate(['full_name' => mb_substr($name, 0, 255)]);
                $achievement->employees()->syncWithoutDetaching($employee->id);
            }
        }

        return redirect()->route('achievements.show', $achievement)
            ->with('status', 'تم حفظ الإنجاز بنجاح.');
    }

    /**
     * Move an uploaded file to the public storage and record it as achievement media.
     */
    private function storeMedia($file, Achievement $achievement, string $type, int $sortOrder): void
    {
        $path = $file->store('uploads/'.$type, 'public');

        AchievementMedia::create([
            'achievement_id' => $achievement->id,
            'type' => $type,
            'source' => AchievementMedia::SOURCE_UPLOAD,
            'url' => $path,
            'original_name' => $file->getClientOriginalName(),
            'sort_order' => $sortOrder,
        ]);
    }

    /**
     * Validation rules shared by store and update.
     */
    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'achievement_at' => ['required', 'date', 'before_or_equal:today'],
            'department_id' => ['required', 'exists:departments,id'],
            'thumbnail' => ['nullable', 'image', 'max:1048576'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:1048576'],
            'videos' => ['nullable', 'array'],
            'videos.*' => ['file', 'mimetypes:video/mp4,video/webm,video/ogg,video/quicktime,video/x-msvideo,video/x-matroska', 'max:1048576'],
            'pdfs' => ['nullable', 'array'],
            'pdfs.*' => ['file', 'mimes:pdf', 'max:204800'],
            'employees' => ['nullable', 'string', 'max:2000'],
            'delete_media' => ['nullable', 'array'],
            'delete_media.*' => ['integer', 'exists:achievement_media,id'],
        ];
    }

    /**
     * Arabic validation messages shared by store and update.
     */
    private function messages(): array
    {
        return [
            'title.required' => 'يرجى كتابة عنوان الإنجاز.',
            'description.required' => 'يرجى كتابة وصف الإنجاز.',
            'achievement_at.required' => 'يرجى تحديد تاريخ الإنجاز.',
            'achievement_at.date' => 'صيغة التاريخ غير صحيحة.',
            'achievement_at.before_or_equal' => 'يجب ألا يتجاوز تاريخ الإنجاز تاريخ اليوم',
            'department_id.required' => 'يرجى اختيار القسم.',
            'department_id.exists' => 'القسم المختار غير موجود.',
            'thumbnail.image' => 'صورة الغلاف يجب أن تكون صورة.',
            'thumbnail.max' => 'حجم صورة الغلاف أكبر من الحد المسموح (1GB).',
            'images.*.image' => 'يُسمح بملفات الصور فقط (JPG, PNG, GIF, WebP...).',
            'images.*.max' => 'حجم الصورة أكبر من الحد المسموح (1GB).',
            'videos.*.file' => 'يرجى رفع ملف صحيح.',
            'videos.*.mimetypes' => 'صيغة الفيديو غير مدعومة (MP4, WebM, Ogg, MOV, AVI, MKV).',
            'videos.*.max' => 'حجم الفيديو أكبر من الحد المسموح (1GB).',
            'pdfs.*.file' => 'يرجى رفع ملف صحيح.',
            'pdfs.*.mimes' => 'يُسمح بملفات PDF فقط.',
            'pdfs.*.max' => 'حجم ملف PDF أكبر من الحد المسموح (200MB).',
            'delete_media.*.exists' => 'ملف الوسائط المحدد للتعديل غير صالح.',
        ];
    }

    /**
     * Show the form to edit an existing achievement (authorized users only).
     */
    public function edit(Request $request, Achievement $achievement): View
    {
        Gate::authorize('manage-achievement', $achievement);

        $achievement->load(['department', 'employees', 'media']);

        return view('achievements.edit', [
            'achievement' => $achievement,
            'departments' => Department::orderBy('sort_order')->get(),
        ]);
    }

    /**
     * Update an achievement, its team and media (add new files, delete existing ones).
     */
    public function update(Request $request, Achievement $achievement): RedirectResponse
    {
        Gate::authorize('manage-achievement', $achievement);

        $data = $request->validate($this->rules(), $this->messages());

        $department = Department::findOrFail($data['department_id']);

        if (! Gate::allows('create-achievement-in', $department)) {
            return back()
                ->withInput()
                ->withErrors(['department_id' => 'غير مسموح بنقل الإنجاز إلى هذا القسم.']);
        }

        $achievement->update([
            'department_id' => $department->id,
            'title' => $data['title'],
            'description' => $data['description'],
            'achievement_at' => $data['achievement_at'],
        ]);

        // Replace the whole contributing team.
        $achievement->employees()->detach();

        if (! empty($data['employees'])) {
            foreach (preg_split('/[\r\n,،]+/u', $data['employees']) as $name) {
                $name = trim($name);
                if ($name === '') {
                    continue;
                }
                $employee = Employee::firstOrCreate(['full_name' => mb_substr($name, 0, 255)]);
                $achievement->employees()->syncWithoutDetaching($employee->id);
            }
        }

        // Delete any media the user checked for removal (and their files).
        if (! empty($data['delete_media'])) {
            $media = $achievement->media()->whereIn('id', $data['delete_media'])->get();

            foreach ($media as $item) {
                if ($item->source === AchievementMedia::SOURCE_UPLOAD) {
                    Storage::disk('public')->delete($item->url);
                }
                $item->delete();
            }
        }

        // A newly chosen thumbnail becomes the first item; shift reaming ones down.
        if ($request->hasFile('thumbnail')) {
            $achievement->media()->where('type', AchievementMedia::TYPE_IMAGE)->increment('sort_order');
            $this->storeMedia($request->file('thumbnail'), $achievement, AchievementMedia::TYPE_IMAGE, 0);
        }

        $sortOrder = ($achievement->media()->max('sort_order') ?? -1) + 1;

        foreach (['images' => AchievementMedia::TYPE_IMAGE, 'videos' => AchievementMedia::TYPE_VIDEO, 'pdfs' => AchievementMedia::TYPE_PDF] as $field => $type) {
            foreach ($request->file($field, []) as $file) {
                $this->storeMedia($file, $achievement, $type, $sortOrder++);
            }
        }

        return redirect()->route('achievements.show', $achievement)
            ->with('status', 'تم تحديث الإنجاز بنجاح.');
    }

    /**
     * Delete an achievement and all its attached media.
     */
    public function destroy(Request $request, Achievement $achievement): RedirectResponse
    {
        Gate::authorize('manage-achievement', $achievement);

        foreach ($achievement->media as $item) {
            if ($item->source === AchievementMedia::SOURCE_UPLOAD) {
                Storage::disk('public')->delete($item->url);
            }
            $item->delete();
        }

        $achievement->employees()->detach();
        $department = $achievement->department;
        $achievement->delete();

        return redirect($department ? route('departments.show', $department->slug) : route('home'))
            ->with('status', 'تم حذف الإنجاز.');
    }
}
