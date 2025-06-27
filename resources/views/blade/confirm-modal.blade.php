{{-- IMPORTANT!!! Make sure there is no newline at the end of this file, or it will break the loaders for the tables --}}

@props([
    'route',
    'id',
    'method',
])
<div {{ $attributes->merge() }} id="dataConfirmModal" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="myModalLabel">&nbsp;</h2>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <form method="post" id="{{ isset($id) ?? $id }}" role="form"{!! isset($route) ?? ' action="'.route($route).'"' !!}>
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}

                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                        {{ trans('general.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-outline" id="dataConfirmOK">
                        {{ trans('general.yes') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>