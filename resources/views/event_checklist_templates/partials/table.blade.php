<div class="clearfix" style="margin: 15px 0;">
    <a href="{{ route('event-checklist-templates.create') }}" class="btn btn-primary pull-right">Create template</a>
</div>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr><th>Name</th><th>Columns</th><th>Template rows</th><th>Used by</th><th></th></tr>
        </thead>
        <tbody>
        @forelse ($templates as $template)
            <tr>
                <td>{{ $template->name }}</td>
                <td>{{ collect($template->columns ?? [])->pluck('label')->implode(', ') }}</td>
                <td>{{ count($template->rows ?? []) }}</td>
                <td>{{ $template->event_checklists_count }} Event Checklist(s)</td>
                <td class="text-right">
                    <a href="{{ route('event-checklist-templates.edit', $template) }}" class="btn btn-sm btn-default">Edit</a>
                    @if ((int) $template->event_checklists_count === 0)
                        <form method="POST" action="{{ route('event-checklist-templates.destroy', $template) }}" class="inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5">No checklist templates found.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $templates->appends(request()->except('templates_page'))->links() }}
</div>
