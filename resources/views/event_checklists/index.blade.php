@extends('layouts/default')

@section('title')
    Event Checklist
    @parent
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-header with-border">
                <div class="box-title">Manage event checklists and reusable templates</div>
            </div>
            <div class="box-body">
                @if ($errors->has('template'))
                    <div class="alert alert-danger">{{ $errors->first('template') }}</div>
                @endif
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="{{ request('tab') !== 'templates' ? 'active' : '' }}">
                        <a href="#checklists" aria-controls="checklists" role="tab" data-toggle="tab">Event Checklists</a>
                    </li>
                    <li role="presentation" class="{{ request('tab') === 'templates' ? 'active' : '' }}">
                        <a href="#templates" aria-controls="templates" role="tab" data-toggle="tab">Checklist Templates</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane {{ request('tab') !== 'templates' ? 'active' : '' }}" id="checklists">
                        <div class="clearfix" style="margin: 15px 0;">
                            <a href="{{ route('event-checklists.create') }}" class="btn btn-primary pull-right">Create checklist</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead><tr><th>Name</th><th>Category</th><th>Transition</th><th></th></tr></thead>
                                <tbody>
                                @forelse ($checklists as $checklist)
                                    <tr>
                                        <td>{{ $checklist->name }}</td>
                                        <td>{{ $checklist->category?->name ?? 'All asset categories' }}</td>
                                        <td>{{ $checklist->fromStatus?->name ?? 'Any' }} &rarr; {{ $checklist->toStatus?->name ?? 'Unknown status' }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('event-checklists.edit', $checklist) }}" class="btn btn-sm btn-default">Edit</a>
                                            <form method="POST" action="{{ route('event-checklists.destroy', $checklist) }}" class="inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <tr class="event-checklist-template">
                                        <td colspan="4">
                                            <strong>Checklist template</strong>
                                            @if (count($checklist->columns ?? []))
                                                <div class="table-responsive">
                                                    <table class="table table-condensed table-bordered">
                                                        <thead>
                                                            <tr>
                                                                @foreach ($checklist->columns as $column)
                                                                    <th>{{ $column['label'] ?? $column['key'] ?? 'Column' }}</th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse ($checklist->rows ?? [] as $row)
                                                                <tr>
                                                                    @foreach ($checklist->columns as $column)
                                                                        @php
                                                                            $key = $column['key'] ?? null;
                                                                            $value = $key ? data_get($row, $key) : null;
                                                                            $displayValue = is_array($value) ? implode(', ', $value) : $value;
                                                                            if (($displayValue === null || $displayValue === '') && ($column['type'] ?? null) === 'select') {
                                                                                $displayValue = implode(', ', $column['options'] ?? []);
                                                                            }
                                                                        @endphp
                                                                        <td>{{ $displayValue !== null && $displayValue !== '' ? $displayValue : '—' }}</td>
                                                                    @endforeach
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="{{ count($checklist->columns) }}">No template rows configured.</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <p class="text-muted">No template columns configured.</p>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4">No event checklists found.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                            {{ $checklists->links() }}
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane {{ request('tab') === 'templates' ? 'active' : '' }}" id="templates">
                        @include('event_checklist_templates.partials.table', ['templates' => $templates])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
