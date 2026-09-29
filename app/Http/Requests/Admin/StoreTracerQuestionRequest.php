<?php

namespace App\Http\Requests\Admin;

use App\Enums\QuestionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTracerQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $type = $this->input('type');
        $requiresOptions = in_array($type, ['radio', 'select', 'checkbox']);

        $rules = [
            'question_text' => ['required', 'string', 'max:500'],
            'type' => ['required', 'string', Rule::in(QuestionType::values())],
            'is_required' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'order' => ['required', 'integer', 'min:0', 'max:9999'],
            'code' => ['nullable', 'string', 'max:50', 'unique:tracer_questions,code'],
        ];

        // Options validation for choice types
        if ($requiresOptions) {
            $rules['options'] = ['required', 'array', 'min:1'];
            $rules['options.*.label'] = ['required', 'string', 'max:255'];
            $rules['options.*.value'] = ['required', 'string', 'max:100'];
            $rules['options.*.order'] = ['sometimes', 'integer', 'min:0'];
            $rules['options.*.is_active'] = ['sometimes', 'boolean'];
        } else {
            $rules['options'] = ['nullable', 'array'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'question_text.required' => 'Pertanyaan harus diisi.',
            'question_text.max' => 'Pertanyaan maksimal 500 karakter.',
            'type.required' => 'Tipe pertanyaan harus dipilih.',
            'type.in' => 'Tipe pertanyaan tidak valid.',
            'order.required' => 'Urutan harus diisi.',
            'order.integer' => 'Urutan harus berupa angka.',
            'order.min' => 'Urutan minimal 0.',
            'code.unique' => 'Kode sudah digunakan.',
            'options.required' => 'Minimal satu pilihan jawaban harus ditambahkan.',
            'options.min' => 'Minimal satu pilihan jawaban harus ditambahkan.',
            'options.*.label.required' => 'Label pilihan jawaban harus diisi.',
            'options.*.value.required' => 'Nilai pilihan jawaban harus diisi.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_required' => $this->boolean('is_required'),
            'is_active' => $this->boolean('is_active'),
        ]);

        // Prepare options array
        $options = [];
        if ($this->has('option_labels') && is_array($this->option_labels)) {
            foreach ($this->option_labels as $index => $label) {
                if (!empty($label)) {
                    $value = $this->option_values[$index] ?? $this->generateValueFromLabel($label);
                    $options[] = [
                        'label' => trim($label),
                        'value' => trim($value) ?: $this->generateValueFromLabel($label),
                        'order' => $index,
                        'is_active' => true,
                    ];
                }
            }
            $this->merge(['options' => $options]);
        }
    }

    /**
     * Generate value from label.
     */
    protected function generateValueFromLabel(string $label): string
    {
        $value = strtolower(trim($label));
        $value = preg_replace('/[^a-z0-9]+/', '_', $value);
        $value = trim($value, '_');
        return $value ?: 'option_' . uniqid();
    }
}
