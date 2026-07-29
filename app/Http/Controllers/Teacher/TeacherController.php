<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\MathClass;
use App\Models\User;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\StudentSubmission;
use App\Models\LearningMaterial;
use App\Models\SchoolYear;
use App\Services\PhilSmsService;

class TeacherController extends Controller
{
    // --- DASHBOARD ---
    public function index()
    {
        $teacher = Auth::user();
        
        $pendingRequestsCount = 0;
        $allClasses = $teacher->taughtClasses()->with(['students' => function ($query) {
            $query->where('class_student.status', 'pending');
        }])->get();

        foreach ($allClasses as $c) {
            $pendingRequestsCount += $c->students->count();
        }

        $classes = $teacher->taughtClasses()->withCount([
            'students as approved_students_count' => function ($query) {
                $query->where('class_student.status', 'approved');
            }
        ])->get();

        $schoolYears = SchoolYear::orderBy('year', 'desc')->get();

        return view('teacher.dashboard', compact('pendingRequestsCount', 'classes', 'schoolYears'));
    }

    // --- SPECIFIC CLASS VIEW ---
    public function showClass($id)
    {
        $teacher = Auth::user();
        $mathClass = $teacher->taughtClasses()->findOrFail($id);

        $assessments = Assessment::where('math_class_id', $id)->orderBy('created_at', 'desc')->get();

        $students = $mathClass->students()
                              ->wherePivot('status', 'approved')
                              ->withPivot('current_risk_level', 'diagnosed_weak_competency')
                              ->get();

        $highRiskCount = 0;
        $approvedStudents = collect();

        foreach ($students as $student) {
            $student->class_name = $mathClass->class_name;
            $student->risk_level = $student->pivot->current_risk_level ?? 'Evaluating...';
            $student->weak_competency = $student->pivot->diagnosed_weak_competency ?? 'None Identified';
            
            if ($student->risk_level === 'High') {
                $highRiskCount++;
            }

            $aiMaterials = LearningMaterial::where('student_id', $student->id)->where('type', 'ai_intervention')->get();
            $student->ai_materials_count = $aiMaterials->count();
            if ($student->ai_materials_count > 0) {
                $completedCount = $aiMaterials->where('status', 'completed')->count();
                $student->ai_progress = round(($completedCount / $student->ai_materials_count) * 100);
            } else {
                $student->ai_progress = 0;
            }

            $approvedStudents->push($student);
        }

        return view('teacher.show_class', compact('mathClass', 'approvedStudents', 'highRiskCount', 'assessments'));
    }

    // --- CLASS MANAGEMENT ---
    public function classes()
    {
        $classes = Auth::user()->taughtClasses()->orderBy('created_at', 'desc')->get();
        $schoolYears = SchoolYear::orderBy('year', 'desc')->get();

        return view('teacher.classes', compact('classes', 'schoolYears'));
    }

    public function storeClass(Request $request)
    {
        $request->validate([
            'class_name' => 'required|string|max:255',
            'section' => 'required|string|max:255',
            'school_year' => 'required|string|max:255',
        ]);

        $code = strtoupper(Str::random(6));
        
        while (MathClass::where('class_code', $code)->exists()) {
            $code = strtoupper(Str::random(6));
        }

        MathClass::create([
            'teacher_id' => Auth::id(),
            'class_name' => $request->class_name,
            'section' => $request->section,
            'school_year' => $request->school_year,
            'class_code' => $code, 
        ]);

        return back()->with('success', "{$request->class_name} ({$request->section}) created successfully. Class Code: {$code}");
    }

    public function updateClass(Request $request, $id)
    {
        $request->validate([
            'class_name' => 'required|string|max:255',
            'section' => 'required|string|max:255',
            'school_year' => 'required|string|max:255',
        ]);

        $teacher = Auth::user();
        $mathClass = $teacher->taughtClasses()->findOrFail($id);

        $mathClass->update([
            'class_name' => $request->class_name,
            'section' => $request->section,
            'school_year' => $request->school_year,
        ]);

        return back()->with('success', "Class details updated successfully.");
    }

    public function destroyClass($id)
    {
        $teacher = Auth::user();
        $mathClass = $teacher->taughtClasses()->findOrFail($id);
        $mathClass->delete();

        return redirect()->route('teacher.dashboard')->with('success', 'Class deleted successfully.');
    }

    // =====================================================================
    // --- PHASE 1: GENERATE & PREVIEW (Steps 1-3) ---
    // =====================================================================
    public function generateAIAssessment(Request $request)
    {
        $request->validate([
            'math_class_id' => 'required|exists:math_classes,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:quiz,activity,assignment,examination',
            'topic' => 'required|string|max:255',
            'part_types' => 'required|array',
            'part_counts' => 'required|array',
            'pdf_document' => 'required|file|mimes:pdf|max:10000'
        ]);

        $tempPath = $request->file('pdf_document')->store('temp_lesson_documents');
        
        $assessmentMetadata = $request->except(['pdf_document', 'part_types', 'part_counts']);
        $assessmentMetadata['temp_pdf_path'] = $tempPath;

        if ($request->has('bypass_ai')) {
            $generatedQuestions = [];
            foreach ($request->part_types as $index => $type) {
                $partNum = $index + 1;
                $count = $request->part_counts[$index];
                
                for ($i = 1; $i <= $count; $i++) {
                    $num1 = rand(2, 12);
                    $num2 = rand(2, 12);
                    $sum = $num1 + $num2;

                    $options = ($type === 'multiple_choice') ? ["A" => $sum, "B" => $sum + 1, "C" => $sum + 2, "D" => $sum - 1] : null;
                    
                    $generatedQuestions[] = [
                        'part_number' => $partNum,
                        'question_type' => $type,
                        'question_text' => "[Offline Test] What is {$num1} + {$num2}?",
                        'options' => $options,
                        'correct_answer' => ($type === 'multiple_choice') ? 'A' : (string)$sum
                    ];
                }
            }
            return view('teacher.preview_assessment', compact('assessmentMetadata', 'generatedQuestions'));
        }

        // Updated API Key Retriever (Checks Config first, falls back to env)
        $geminiKey = trim(config('services.gemini.key') ?? env('GEMINI_API_KEY'));
        if (empty($geminiKey)) {
            return back()->with('error', 'System Error: GEMINI_API_KEY is missing from your .env file.');
        }

        $structureInstructions = "";
        foreach ($request->part_types as $index => $type) {
            $partNum = $index + 1;
            $count = $request->part_counts[$index];
            $structureInstructions .= "- Part {$partNum}: {$count} questions of type '{$type}'.\n";
        }
        
        $prompt = "You are an expert Mathematics Educator for all school levels. Create a math assessment based on the learning objective '{$request->topic}'. 
        Follow this exact structure:
        {$structureInstructions}
        
        Rules for question types:
        - 'multiple_choice': Provide 'part_number', 'question_type' (as 'multiple_choice'), 'question_text', an 'options' object strictly like {\"A\": \"First Option\", \"B\": \"Second Option\"}, and 'correct_answer' (e.g., 'A').
        - 'fill_in_the_blank': Provide 'part_number', 'question_type' (as 'fill_in_the_blank'), 'question_text' (use ___ for the blank), 'options' MUST be null, and 'correct_answer' is the exact word/number.
        - 'problem_solving' or 'computation': Provide 'part_number', 'question_type' (as 'problem_solving' or 'computation'), a math problem as 'question_text', 'options' MUST be null, and 'correct_answer' is the final numerical result only.

        You must return a valid JSON object with a single root key called \"questions\" containing an array of these objects.";

        try {
            $response = Http::withoutVerifying()->timeout(60)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$geminiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['response_mime_type' => 'application/json']
                ]);

            if ($response->successful()) {
                $rawText = $response->json()['candidates'][0]['content']['parts'][0]['text'];
                $parsedData = json_decode($rawText, true);

                if (isset($parsedData['questions'])) {
                    $generatedQuestions = $parsedData['questions'];
                    return view('teacher.preview_assessment', compact('assessmentMetadata', 'generatedQuestions'));
                }
            }
            return back()->with('error', 'AI Generation Failed: Invalid JSON returned from Gemini.');
        } catch (\Exception $e) {
            return back()->with('error', 'Server Error: ' . $e->getMessage());
        }
    }

    // =====================================================================
    // --- PHASE 2: ML ANALYSIS & DEPLOY (Steps 4-6) ---
    // =====================================================================
    public function storeAIAssessment(Request $request)
    {
        $questions = $request->input('questions');
        $metadata = $request->input('metadata');

        $difficultyScore = 0.50; 
        $expectedPassRate = 75.00; 

        try {
            $mlAnalysis = Http::withoutVerifying()->timeout(10)->post('http://127.0.0.1:5000/analyze-difficulty', [
                'questions' => $questions
            ]);

            if ($mlAnalysis->successful()) {
                $difficultyScore = $mlAnalysis->json()['overall_difficulty'];
                $expectedPassRate = $mlAnalysis->json()['expected_pass_rate'];
            }
        } catch (\Exception $e) {
            // Proceed with defaults if Python server is unreachable
        }

        $finalPdfPath = str_replace('temp_lesson_documents', 'lesson_documents', $metadata['temp_pdf_path']);
        if (\Illuminate\Support\Facades\Storage::exists($metadata['temp_pdf_path'])) {
            \Illuminate\Support\Facades\Storage::move($metadata['temp_pdf_path'], $finalPdfPath);
        }

        $assessment = Assessment::create([
            'math_class_id' => $metadata['math_class_id'],
            'title' => $metadata['title'],
            'type' => $metadata['type'],
            'topic' => $metadata['topic'],
            'pdf_path' => $finalPdfPath,
            'difficulty_score' => $difficultyScore,
            'expected_pass_rate' => $expectedPassRate
        ]);

        foreach ($questions as $q) {
            AssessmentQuestion::create([
                'assessment_id' => $assessment->id,
                'part_number' => $q['part_number'] ?? 1,
                'question_type' => $q['question_type'] ?? 'computation',
                'question_text' => $q['question_text'] ?? '',
                'options' => !empty($q['options']) ? (is_string($q['options']) ? json_decode($q['options'], true) : $q['options']) : null,
                'correct_answer' => $q['correct_answer'] ?? ''
            ]);
        }

        $mathClass = MathClass::with('students')->findOrFail($metadata['math_class_id']);
        
        foreach ($mathClass->students as $student) {
            $classSubmissions = StudentSubmission::where('student_id', $student->id)
                ->whereHas('assessment', function ($query) use ($assessment) {
                    $query->where('math_class_id', $assessment->math_class_id);
                })->get();

            if ($classSubmissions->isNotEmpty()) {
                $movingAverage = $classSubmissions->avg('calculated_score');
                
                $riskLevel = 'Low';
                $adjustedHighThreshold = 60 - (($difficultyScore - 0.50) * 20); 
                $adjustedModerateThreshold = 75 - (($difficultyScore - 0.50) * 15);

                if ($movingAverage < $adjustedModerateThreshold) $riskLevel = 'Moderate';
                if ($movingAverage < $adjustedHighThreshold) $riskLevel = 'High';

                $student->enrolledClasses()->updateExistingPivot($assessment->math_class_id, [
                    'current_risk_level' => $riskLevel
                ]);
            }
        }

        return redirect()->route('teacher.classes.show', $assessment->math_class_id)
            ->with('success', "Assessment deployed successfully! ML calculated a difficulty rating of " . ($difficultyScore * 100) . "%.");
    }

    // --- ENROLLMENT REQUESTS ---
    public function joinRequests()
    {
        $classes = Auth::user()->taughtClasses()->with(['students' => function ($query) {
            $query->wherePivot('status', 'pending');
        }])->get();

        return view('teacher.requests', compact('classes'));
    }

    public function updateRequest(Request $request, $class_id, $student_id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $mathClass = Auth::user()->taughtClasses()->findOrFail($class_id);
        $mathClass->students()->updateExistingPivot($student_id, ['status' => $request->status]);

        return back()->with('success', 'Student enrollment status updated.');
    }

    public function removeStudent($class_id, $student_id)
    {
        $teacher = Auth::user();
        $mathClass = $teacher->taughtClasses()->findOrFail($class_id);
        $mathClass->students()->detach($student_id);
        return back()->with('success', 'Student successfully removed from the class roster.');
    }

    public function updateAssessment(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:quiz,activity,assignment,examination',
            'topic' => 'required|string|max:255',
        ]);
        $assessment = Assessment::findOrFail($id);
        $assessment->update($request->only('title', 'type', 'topic'));
        return back()->with('success', 'Assessment details updated successfully.');
    }

    public function destroyAssessment($id)
    {
        $assessment = Assessment::findOrFail($id);
        $assessment->delete();
        return back()->with('success', 'Assessment permanently deleted.');
    }

    public function studentAnalytics($class_id, $student_id)
    {
        $teacher = Auth::user();
        $mathClass = $teacher->taughtClasses()->findOrFail($class_id);
        $student = $mathClass->students()->wherePivot('status', 'approved')->withPivot('current_risk_level', 'diagnosed_weak_competency')->findOrFail($student_id);
        $classAssessmentIds = Assessment::where('math_class_id', $class_id)->pluck('id');
        $submissions = StudentSubmission::where('student_id', $student_id)->whereIn('assessment_id', $classAssessmentIds)->with('assessment')->orderBy('created_at', 'desc')->get();
        
        return view('teacher.student_analytics', compact('mathClass', 'student', 'submissions'));
    }

    // --- GENERATE AI INTERVENTION ---
    public function generateIntervention($student_id)
    {
        $latestSubmission = StudentSubmission::where('student_id', $student_id)
            ->whereNotNull('ai_weakness_diagnosis')
            ->where('ai_weakness_diagnosis', '!=', 'None Identified')
            ->latest()
            ->first();

        $weakness = $latestSubmission ? $latestSubmission->ai_weakness_diagnosis : "Foundational Mathematics Concepts";
        
        // Updated API Key Retriever
        $geminiKey = trim(config('services.gemini.key') ?? env('GEMINI_API_KEY'));
        if (empty($geminiKey)) { 
            return back()->with('error', 'System Error: GEMINI_API_KEY is missing.'); 
        }

        $prompt = "Act as a professional Mathematics Curriculum Designer. A student requires a targeted remedial module for the following learning objective: '{$weakness}'. 
        Write a structured, academic intervention lesson focused strictly on MATHEMATICAL OPERATIONS, FORMULAS, and EQUATIONS. 
        Do NOT use text-heavy problem-solving frameworks (like the 4-step method). Teach the math through direct calculation and operational logic.
        
        Strictly follow this pedagogical structure:
        1. Concept & Formula: Provide a clear, formal definition of the concept and state the exact mathematical formula or operational equation required.
        2. Operational Breakdown: Provide two distinct mathematical examples. Show the exact step-by-step calculations. Emphasize the numbers and operations; keep textual explanation brief and strictly tied to the calculation.
        3. Independent Practice: Provide exactly three numerical practice equations or direct operational problems for the student to solve on their own.

        Format the ENTIRE response in clean HTML using ONLY <h3>, <h4>, <p>, <ul>, <ol>, <li>, and <strong> tags. Format mathematical steps clearly on their own lines. Do not output any markdown formatting (like ```html). Output ONLY the lesson content.";

        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent";

        try {
            $response = Http::withoutVerifying()->timeout(60)                                 ->withHeaders(['Content-Type' => 'application/json', 'x-goog-api-key' =>$geminiKey])
                ->post($apiUrl, ['contents' => [['parts' => [['text' =>$prompt]]]]]);

            if ($response->successful()) {$rawHtml = $response->json()['candidates'][0]['content']['parts'][0]['text'];$cleanHtml = preg_replace('/```(html)?/', '', $rawHtml);

                \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
                LearningMaterial::create([
                    'student_id' => $student_id,
                    'competency_id' => 0, 
                    'title' => "AI Tutorial: Fixing " . $weakness,
                    'content' => trim($cleanHtml),
                    'type' => 'ai_intervention',
                    'status' => 'pending',
                    'content_url' => '#' 
                ]);
                \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

                return back()->with('success', 'A personalized learning module was generated and assigned to the student.');
            } else {
                $errorData = $response->json();
                return back()->with('error', 'API Error: ' . ($errorData['error']['message'] ?? 'Unknown Gemini API Error'));
            }
        } catch (\Exception $e) {
            return back()->with('error', 'System Error: ' . $e->getMessage());
        }
    }

    public function editIntervention($id) 
    { 
        $intervention = LearningMaterial::findOrFail($id); 
        return view('teacher.edit_intervention', compact('intervention')); 
    }
    
    public function updateIntervention(Request $request, $id) 
    {
        $request->validate(['title' => 'required|string|max:255', 'content' => 'required|string']);
        $intervention = LearningMaterial::findOrFail($id);
        $intervention->update(['title' => $request->title, 'content' => $request->content]);
        return redirect()->route('teacher.dashboard')->with('success', 'AI Learning Module updated successfully!');
    }
    
    public function destroyIntervention($id) 
    {
        $intervention = LearningMaterial::findOrFail($id);
        $intervention->delete();
        return back()->with('success', 'AI Learning Module deleted successfully.');
    }
    
    // --- SMS LOGIC ---
    public function directMessageParent(Request $request, $studentId, PhilSmsService $smsService)
    {
        $request->validate([
            'custom_message' => 'required|string|max:300'
        ]);

        $student = User::findOrFail($studentId);
        
        $phoneNumber = $student->phone ?? $student->contact_number;

        if (empty($phoneNumber) && $student->parent_id) {
            $parent = User::find($student->parent_id);
            $phoneNumber = $parent->phone ?? $parent->contact_number ?? null;
        }

        if (empty($phoneNumber)) {
            return back()->with('error', 'Cannot send SMS: No phone number is linked to this student.');
        }

        $finalMessage = "PrognosMath Alert: " . $request->custom_message;
        $smsSent = $smsService->sendSms($phoneNumber, $finalMessage);

        if ($smsSent) {
            return back()->with('success', 'Direct message sent successfully to ' . $phoneNumber . '!');
        }

        return back()->with('error', 'Failed to send SMS. Please check your API credits or system logs.');
    }
}