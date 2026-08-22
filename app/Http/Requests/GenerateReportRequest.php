<?php

namespace App\Http\Requests;

use App\Data\ReportGenerateData;
use Illuminate\Foundation\Http\FormRequest;

class GenerateReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'string', 'in:sales,inventory,performance'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => __('attribute.title'),
            'type' => __('attribute.type'),
        ];
    }

    public function toDTO(): ReportGenerateData
    {
        $validated = $this->validated();

        return ReportGenerateData::from([
            'title' => $validated['title'] ?? 'Sales Report - ' . now()->format('d M Y H:i'),
            'type' => $validated['type'] ?? 'sales',
        ]);
    }
}
