<div id="checkout-user-details">
    @include ('partials.forms.edit.location-select', [
        'translated_name' => trans('general.location'),
        'fieldname' => 'checkout_location_id'
    ])

    @foreach (['Loc. Detail', 'On Behalf'] as $fieldName)
        @if ($field = $checkoutCustomFields->get($fieldName))
            <div class="form-group{{ $errors->has($field->db_column_name()) ? ' has-error' : '' }}">
                <label for="{{ $field->db_column_name() }}" class="col-md-3 control-label">
                    {{ $field->name }}
                </label>
                <div class="col-md-7 col-sm-12">
                    <input
                        type="text"
                        class="form-control"
                        id="{{ $field->db_column_name() }}"
                        name="{{ $field->db_column_name() }}"
                        value="{{ old($field->db_column_name(), isset($item) ? $item->{$field->db_column_name()} : '') }}"
                    >
                    {!! $errors->first($field->db_column_name(), '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                </div>
            </div>
        @endif
    @endforeach
</div>

<script nonce="{{ csrf_token() }}">
    $(function () {
        const $userSelector = $('#assigned_user_select');
        const $locationSelector = $('#checkout_location_id_location_select');
        const syncCheckoutUserDetails = function () {
            $('#checkout-user-details').toggle($('input[name="checkout_to_type"]:checked').val() === 'user');
        };

        $('input[name="checkout_to_type"]').on('change', syncCheckoutUserDetails);
        syncCheckoutUserDetails();

        const syncUserLocation = function (userId) {
            if (!userId) {
                $locationSelector.empty().trigger('change');
                return;
            }

            $.ajax({
                url: '{{ url('/api/v1/users') }}/' + encodeURIComponent(userId),
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).done(function (user) {
                if (String($userSelector.val()) !== String(userId)) {
                    return;
                }

                $locationSelector.empty();
                if (user.location && user.location.id && user.location.name) {
                    $locationSelector.append(new Option(user.location.name, user.location.id, true, true));
                }
                $locationSelector.trigger('change');
            }).fail(function (xhr) {
                console.error('Unable to load the selected user location.', xhr);
            });
        };

        $userSelector.on('select2:select', function (event) {
            syncUserLocation(event.params.data.id);
        });
        $userSelector.on('select2:clear', function () {
            syncUserLocation(null);
        });

        if ($userSelector.val()) {
            syncUserLocation($userSelector.val());
        }
    });
</script>
