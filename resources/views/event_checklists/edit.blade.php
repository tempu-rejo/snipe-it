@extends('layouts/default')

@section('title')
    {{ $item->exists ? 'Edit' : 'Create' }} Event Checklist
    @parent
@stop

@section('content')
<div class="row"><div class="col-md-10 col-md-offset-1"><div class="box box-default"><div class="box-body">
<form method="POST" action="{{ $item->exists ? route('event-checklists.update', $item) : route('event-checklists.store') }}">
    @csrf
    @if ($item->exists) @method('PUT') @endif
    <div class="form-group"><label>Name</label><input class="form-control" name="name" value="{{ old('name', $item->name) }}" required></div>
    <div class="form-group"><label>Inventory category <small>(optional)</small></label><select class="form-control" name="category_id"><option value="">All asset categories</option>@foreach($categories as $id => $name)<option value="{{ $id }}" @selected(old('category_id', $item->category_id) == $id)>{{ $name }}</option>@endforeach</select></div>
    <div class="form-group"><label>From status (optional)</label><select class="form-control" name="from_status_id"><option value="">Any status</option>@foreach($statuses as $id => $name)<option value="{{ $id }}" @selected(old('from_status_id', $item->from_status_id) == $id)>{{ $name }}</option>@endforeach</select></div>
    <div class="form-group"><label>To status</label><select class="form-control" name="to_status_id" required><option value="">Select</option>@foreach($statuses as $id => $name)<option value="{{ $id }}" @selected(old('to_status_id', $item->to_status_id) == $id)>{{ $name }}</option>@endforeach</select></div>
    <div class="form-group"><label>Checklist template</label><select class="form-control" name="template_id" required><option value="">Select a template</option>@foreach($templates as $id => $name)<option value="{{ $id }}" @selected(old('template_id', $item->template_id) == $id)>{{ $name }}</option>@endforeach</select><p class="help-block">Manage reusable templates from the Checklist Templates tab in Event Checklist.</p>@if($templates->isEmpty())<p class="text-danger">Create a checklist template before creating an Event Checklist.</p>@endif</div>
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn btn-default" href="{{ route('event-checklists.index') }}">Cancel</a>
</form>
</div></div></div></div>
@stop
