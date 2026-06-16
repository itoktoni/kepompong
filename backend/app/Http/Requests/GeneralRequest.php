<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeneralRequest extends FormRequest
{
    public function authorize(): bool
    {
        $model = request()->route()->getController()->model;
        $action = str_replace(['get', 'post'], '', strtolower(request()->route()->getActionMethod()));

        return $this->user()->can($action, $model);
    }

    public function rules(): array
    {
        return [];
    }
}
