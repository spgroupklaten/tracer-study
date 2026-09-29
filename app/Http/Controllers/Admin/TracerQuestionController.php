<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTracerQuestionRequest;
use App\Http\Requests\Admin\UpdateTracerQuestionRequest;
use App\Models\TracerQuestion;
use App\Models\TracerQuestionOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TracerQuestionController extends Controller
{
    /**
     * Display a listing of the questions.
     */
    public function index(Request $request): View
    {
        $questions = TracerQuestion::query()
            ->withCount('questionOptions')
            ->when($request->has('search') && $request->search, function ($query) use ($request) {
                $query->where('question_text', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            })
            ->when($request->has('type') && $request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->when($request->has('status') && $request->status !== '', function ($query) use ($request) {
                $query->where('is_active', $request->status === 'active');
            })
            ->orderBy('order')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.kuesioner.index', [
            'questions' => $questions,
            'filters' => $request->only(['search', 'type', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new question.
     */
    public function create(): View
    {
        return view('admin.kuesioner.create');
    }

    /**
     * Store a newly created question in storage.
     */
    public function store(StoreTracerQuestionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Generate code if not provided
        if (empty($validated['code'])) {
            $validated['code'] = 'q_' . uniqid();
        }

        // Extract options before creating question
        $options = $validated['options'] ?? [];
        unset($validated['options']);

        // Create question
        $question = TracerQuestion::create($validated);

        // Create options
        foreach ($options as $index => $option) {
            $question->questionOptions()->create([
                'label' => $option['label'],
                'value' => $option['value'],
                'order' => $option['order'] ?? $index,
                'is_active' => $option['is_active'] ?? true,
            ]);
        }

        return redirect()
            ->route('admin.kuesioner.index')
            ->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified question.
     */
    public function edit(TracerQuestion $question): View
    {
        $question->load('questionOptions');

        return view('admin.kuesioner.edit', [
            'question' => $question,
        ]);
    }

    /**
     * Update the specified question in storage.
     */
    public function update(UpdateTracerQuestionRequest $request, TracerQuestion $question): RedirectResponse
    {
        $validated = $request->validated();

        // Only generate code if provided and empty
        if (array_key_exists('code', $validated) && empty($validated['code'])) {
            $validated['code'] = 'q_' . uniqid();
        }

        // Extract options before updating question
        $options = $validated['options'] ?? [];
        unset($validated['options']);

        // Update question
        $question->update($validated);

        // Sync options
        $this->syncOptions($question, $options);

        return redirect()
            ->route('admin.kuesioner.index')
            ->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    /**
     * Sync options for a question.
     */
    protected function syncOptions(TracerQuestion $question, array $options): void
    {
        $existingIds = $question->questionOptions()->pluck('id')->toArray();
        $newIds = [];

        foreach ($options as $index => $optionData) {
            // Skip deleted options
            if (!empty($optionData['_delete'])) {
                continue;
            }

            if (!empty($optionData['id']) && in_array($optionData['id'], $existingIds)) {
                // Update existing option
                $option = TracerQuestionOption::find($optionData['id']);

                // Check if option has answers and is being deactivated
                if ($option->answers()->exists() && isset($optionData['is_active']) && !$optionData['is_active']) {
                    // Option has answers - don't deactivate, just update order
                    $option->update([
                        'order' => $optionData['order'] ?? $index,
                        'label' => $optionData['label'],
                        'value' => $optionData['value'],
                    ]);
                } else {
                    $option->update([
                        'label' => $optionData['label'],
                        'value' => $optionData['value'],
                        'order' => $optionData['order'] ?? $index,
                        'is_active' => $optionData['is_active'] ?? true,
                    ]);
                }

                $newIds[] = $optionData['id'];
            } else {
                // Create new option
                $newOption = $question->questionOptions()->create([
                    'label' => $optionData['label'],
                    'value' => $optionData['value'],
                    'order' => $optionData['order'] ?? $index,
                    'is_active' => $optionData['is_active'] ?? true,
                ]);
                $newIds[] = $newOption->id;
            }
        }

        // Delete options that are marked for deletion
        $optionsToDelete = $question->questionOptions()
            ->whereNotIn('id', $newIds)
            ->whereDoesntHave('answers')
            ->pluck('id')
            ->toArray();

        if (!empty($optionsToDelete)) {
            TracerQuestionOption::whereIn('id', $optionsToDelete)->delete();
        }
    }

    /**
     * Remove the specified question from storage.
     */
    public function destroy(TracerQuestion $question): RedirectResponse
    {
        // Check if question has answers
        if ($question->answers()->exists()) {
            return redirect()
                ->route('admin.kuesioner.index')
                ->with('error', 'Pertanyaan tidak dapat dihapus karena sudah memiliki jawaban Tracer Study. Nonaktifkan pertanyaan sebagai gantinya.');
        }

        $question->delete();

        return redirect()
            ->route('admin.kuesioner.index')
            ->with('success', 'Pertanyaan berhasil dihapus.');
    }

    /**
     * Toggle the active status of the specified question.
     */
    public function toggleStatus(TracerQuestion $question): RedirectResponse
    {
        $question->update([
            'is_active' => !$question->is_active,
        ]);

        $status = $question->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->route('admin.kuesioner.index')
            ->with('success', "Pertanyaan berhasil {$status}.");
    }
}
