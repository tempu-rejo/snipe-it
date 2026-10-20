<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\EventChecklist;
use App\Models\EventChecklistTemplate;
use App\Models\EventChecklistSubmission;
use App\Models\Statuslabel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EventChecklistsController extends Controller
{
    public function index(): View
    {
        $this->authorize('view', Category::class);

        return view('event_checklists.index', [
            'checklists' => EventChecklist::with(['category', 'fromStatus', 'toStatus', 'template'])->latest()->paginate(25),
            'templates' => EventChecklistTemplate::withCount('eventChecklists')
                ->orderBy('name')
                ->paginate(25, ['*'], 'templates_page'),
        ]);
    }

    public function create(): View
    {
        $this->authorize('update', Category::class);

        return view('event_checklists.edit', [
            'item' => new EventChecklist(),
            'categories' => Category::where('category_type', 'asset')->orderBy('name')->pluck('name', 'id'),
            'statuses' => Statuslabel::orderBy('name')->pluck('name', 'id'),
            'templates' => EventChecklistTemplate::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('update', Category::class);
        $checklist = new EventChecklist();
        $this->saveChecklist($request, $checklist);

        return redirect()->route('event-checklists.index')->with('success', 'Event checklist created.');
    }

    public function edit(EventChecklist $eventChecklist): View
    {
        $this->authorize('update', Category::class);

        return view('event_checklists.edit', [
            'item' => $eventChecklist,
            'categories' => Category::where('category_type', 'asset')->orderBy('name')->pluck('name', 'id'),
            'statuses' => Statuslabel::orderBy('name')->pluck('name', 'id'),
            'templates' => EventChecklistTemplate::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function update(Request $request, EventChecklist $eventChecklist): RedirectResponse
    {
        $this->authorize('update', Category::class);
        $this->saveChecklist($request, $eventChecklist);

        return redirect()->route('event-checklists.index')->with('success', 'Event checklist updated.');
    }

    public function destroy(EventChecklist $eventChecklist): RedirectResponse
    {
        $this->authorize('update', Category::class);
        $eventChecklist->delete();

        return redirect()->route('event-checklists.index')->with('success', 'Event checklist deleted.');
    }

    public function downloadEvidence(EventChecklistSubmission $submission, string $row): BinaryFileResponse|RedirectResponse
    {
        $this->authorize('view', $submission->asset);

        if (preg_match('/^header-(\d+)$/', $row, $matches)) {
            $file = $submission->evidence_files[(int) $matches[1]] ?? null;
        } elseif ($row === 'header') {
            $file = $submission->evidence_files[0] ?? null;
        } else {
            $file = $submission->rows[(int) $row]['evidence_file'] ?? null;
        }

        if (is_array($file) && ! empty($file['upload_id'])) {
            abort_unless(
                $submission->asset->uploads()->whereKey($file['upload_id'])->exists(),
                404
            );

            return redirect()->route('show/assetfile', [$submission->asset_id, $file['upload_id']]);
        }

        abort_unless(
            is_array($file)
            && isset($file['path'], $file['name'])
            && Storage::disk('local')->exists($file['path']),
            404
        );

        $downloadName = str_replace(['/', '\\'], '_', $file['name']);
        $downloadName = preg_replace('/[\x00-\x1F\x7F]/', '_', $downloadName) ?: 'evidence';

        return response()->download(Storage::disk('local')->path($file['path']), $downloadName);
    }

    private function saveChecklist(Request $request, EventChecklist $checklist): void
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'from_status_id' => ['nullable', 'integer', 'exists:status_labels,id'],
            'to_status_id' => ['required', 'integer', 'exists:status_labels,id'],
            'template_id' => ['required', 'integer', 'exists:event_checklist_templates,id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $checklist->fill($data)->save();
    }
}
