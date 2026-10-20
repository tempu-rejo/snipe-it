@extends('layouts/default')

@section('title')
    {{ $item->exists ? 'Edit' : 'Create' }} Checklist Template
    @parent
@stop

@section('content')
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="box box-default">
            <div class="box-header with-border">
                <h2 class="box-title">{{ $item->exists ? 'Edit' : 'Create' }} Checklist Template</h2>
            </div>
            <div class="box-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="list-unstyled">
                            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ $item->exists ? route('event-checklist-templates.update', $item) : route('event-checklist-templates.store') }}">
                    @csrf
                    @if ($item->exists) @method('PUT') @endif
                    <div class="form-group">
                        <label for="template-name">Template name</label>
                        <input id="template-name" class="form-control" name="name" value="{{ old('name', $item->name) }}" required maxlength="255">
                    </div>
                    <p class="help-block">Define each column with a unique <code>key</code>, a display <code>label</code>, and a type. Select columns can include an <code>options</code> array.</p>
                    <div class="form-group">
                        <label for="template-columns">Columns (JSON)</label>
                        <textarea id="template-columns" class="form-control" name="columns" rows="14" required>{{ old('columns', json_encode($item->columns ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) }}</textarea>
                    </div>
                    <p class="help-block">Template rows are objects keyed by column key. Leave editable answers blank where users should enter a result during the checklist.</p>
                    <div class="form-group">
                        <label for="template-rows">Template rows (JSON)</label>
                        <textarea id="template-rows" class="form-control" name="rows" rows="18" required>{{ old('rows', json_encode($item->rows ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) }}</textarea>
                    </div>
                    <button class="btn btn-primary" type="submit">Save</button>
                    <a class="btn btn-default" href="{{ route('event-checklist-templates.index') }}">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
