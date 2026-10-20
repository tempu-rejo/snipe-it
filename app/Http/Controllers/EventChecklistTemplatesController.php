<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\EventChecklistTemplate;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use JsonException;

class EventChecklistTemplatesController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('event-checklists.index', ['tab' => 'templates']);
    }

    public function create(): View
    {
        $this->authorize('update', Category::class);

        return view('event_checklist_templates.edit', [
            'item' => new EventChecklistTemplate([
                'columns' => [
                    ['key' => 'checkpoint', 'label' => 'Checklist item', 'type' => 'textarea', 'required' => true],
                    ['key' => 'result', 'label' => 'Result', 'type' => 'select', 'required' => true, 'options' => ['PASS', 'FAIL', 'PENDING', 'N/A']],
                ],
                'rows' => [['checkpoint' => '', 'result' => null]],
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('update', Category::class);

        $template = new EventChecklistTemplate();
        $this->saveTemplate($request, $template);

        return $this->redirectToTemplates()->with('success', 'Checklist template created.');
    }

    public function edit(EventChecklistTemplate $eventChecklistTemplate): View
    {
        $this->authorize('update', Category::class);

        return view('event_checklist_templates.edit', ['item' => $eventChecklistTemplate]);
    }

    public function update(Request $request, EventChecklistTemplate $eventChecklistTemplate): RedirectResponse
    {
        $this->authorize('update', Category::class);
        $this->saveTemplate($request, $eventChecklistTemplate);

        return $this->redirectToTemplates()->with('success', 'Checklist template updated.');
    }

    public function destroy(EventChecklistTemplate $eventChecklistTemplate): RedirectResponse
    {
        $this->authorize('update', Category::class);

        if ($eventChecklistTemplate->eventChecklists()->exists()) {
            return $this->redirectToTemplates()
                ->withErrors(['template' => 'This template is assigned to an Event Checklist and cannot be deleted.']);
        }

        $eventChecklistTemplate->delete();

        return $this->redirectToTemplates()->with('success', 'Checklist template deleted.');
    }

    private function redirectToTemplates(): RedirectResponse
    {
        return redirect()->route('event-checklists.index', ['tab' => 'templates']);
    }

    private function saveTemplate(Request $request, EventChecklistTemplate $template): void
    {
        $input = $request->all();
        foreach (['columns', 'rows'] as $field) {
            if (! is_string($input[$field] ?? null)) {
                continue;
            }

            try {
                $input[$field] = json_decode($input[$field], true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                throw ValidationException::withMessages([$field => 'Enter valid JSON.']);
            }
        }

        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'columns' => ['required', 'array', 'min:1'],
            'columns.*.key' => ['required', 'alpha_dash', 'max:100'],
            'columns.*.label' => ['required', 'string', 'max:255'],
            'columns.*.type' => ['required', 'in:text,textarea,number,date,select,checkbox'],
            'columns.*.required' => ['nullable', 'boolean'],
            'columns.*.options' => ['nullable', 'array'],
            'columns.*.options.*' => ['string', 'max:255'],
            'rows' => ['required', 'array'],
            'rows.*' => ['array'],
        ]);

        $validator->after(function ($validator) use ($input): void {
            $columns = $input['columns'] ?? [];
            $rows = $input['rows'] ?? [];

            if (! is_array($columns) || ! is_array($rows)) {
                return;
            }

            $keys = collect($columns)->pluck('key');
            if ($keys->count() !== $keys->unique()->count()) {
                $validator->errors()->add('columns', 'Checklist column keys must be unique.');
            }

            $columnsByKey = collect($columns)->keyBy('key');
            foreach ($rows as $rowIndex => $row) {
                if (! is_array($row)) {
                    continue;
                }

                foreach ($row as $key => $value) {
                    $column = $columnsByKey->get($key);
                    if (! is_array($column)) {
                        $validator->errors()->add("rows.$rowIndex", "The row contains an unknown column: $key.");
                        continue;
                    }

                    if (! is_null($value) && ! is_scalar($value)) {
                        $validator->errors()->add("rows.$rowIndex.$key", 'Template values must be simple text or numbers.');
                        continue;
                    }

                    $options = is_array($column['options'] ?? null) ? $column['options'] : [];
                    if (($column['type'] ?? null) === 'select'
                        && $value !== null
                        && $value !== ''
                        && ! in_array((string) $value, $options, true)) {
                        $validator->errors()->add("rows.$rowIndex.$key", 'The template value must match one of the column options.');
                    }
                }
            }
        });

        $template->fill($validator->validate())->saveOrFail();
    }
}
