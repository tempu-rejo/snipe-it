<?php

namespace App\Http\Requests;

class AssetCheckinRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $settings = \App\Models\Setting::getSettings();

        $rules = [];

            $rules['status_id'] = 'nullable|integer|exists:status_labels,id';

            if($settings->require_checkinout_notes) {
            $rules['note'] = 'string|required';
        }

            $rules['file.*'] = 'required|mimes:png,gif,jpg,svg,jpeg,doc,docx,pdf,txt,zip,rar,xls,xlsx,lic,xml,rtf,json,webp,avif|max:'.\App\Helpers\Helper::file_upload_max_size();

            return $rules;
    }

    public function response(array $errors)
    {
        return $this->redirector->back()->withInput()->withErrors($errors, $this->errorBag);
    }
}
