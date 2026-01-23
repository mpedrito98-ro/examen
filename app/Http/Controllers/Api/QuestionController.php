<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->query('paginate') === 'false') {
            return Question::with('answers')->latest()->get();
        }
        return Question::with('answers')->latest()->paginate(10);
    }

    public function store(Request $request)
    {
        $request->validate([
            'question_text' => 'required|string',
            'answers' => 'required|array|min:2',
            'answers.*.answer_text' => 'required|string',
            'answers.*.is_correct' => 'required|boolean',
        ]);

        return DB::transaction(function () use ($request) {
            $question = Question::create([
                'question_text' => $request->question_text,
            ]);

            foreach ($request->answers as $answerData) {
                $question->answers()->create($answerData);
            }

            return response()->json($question->load('answers'), 201);
        });
    }

    public function show(Question $question)
    {
        return $question->load('answers');
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'question_text' => 'required|string',
            'answers' => 'required|array|min:2',
            'answers.*.id' => 'sometimes|integer',
            'answers.*.answer_text' => 'required|string',
            'answers.*.is_correct' => 'required|boolean',
        ]);

        return DB::transaction(function () use ($request, $question) {
            $question->update([
                'question_text' => $request->question_text,
            ]);

            $existingAnswerIds = $question->answers->pluck('id')->toArray();
            $incomingAnswerIds = collect($request->answers)->pluck('id')->filter()->toArray();

            // Delete answers that are not in the incoming request
            $idsToDelete = array_diff($existingAnswerIds, $incomingAnswerIds);
            if (!empty($idsToDelete)) {
                $question->answers()->whereIn('id', $idsToDelete)->delete();
            }

            foreach ($request->answers as $answerData) {
                if (isset($answerData['id'])) {
                    // Update existing answer
                    $answer = $question->answers()->find($answerData['id']);
                    if ($answer) {
                        $answer->update($answerData);
                    }
                } else {
                    // Create new answer
                    $question->answers()->create($answerData);
                }
            }

            return $question->load('answers');
        });
    }



    public function destroy(Question $question)
    {
        $question->delete();

        return response()->noContent();
    }
}
