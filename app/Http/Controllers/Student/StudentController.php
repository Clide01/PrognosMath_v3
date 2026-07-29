<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\MathClass;
use App\Models\LearningMaterial;
use App\Models\Grade;
use App\Models\Assessment;
use App\Models\StudentSubmission;

class StudentController extends Controller
{
    // --- DASHBOARD ---
    public function index()
    {
        $student = Auth::user();
        
        $enrolledClasses = $student->enrolledClasses()
                                   ->wherePivot('status', 'approved')
                                   ->withPivot('current_risk_level', 'diagnosed_weak_competency')
                                   ->get();
        
        $recentGrades = Grade::where('student_id', $student->id)
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();

        $completedAssessmentIds = StudentSubmission::where('student_id', $student->id)
                                    ->pluck('assessment_id')
                                    ->toArray();

        $classIds = $enrolledClasses->pluck('id');
        $activeAssessments = Assessment::whereIn('math_class_id', $classIds)
                                ->whereNotIn('id', $completedAssessmentIds)
                                ->orderBy('created_at', 'desc')
                                ->get();

        // Fetch AI Tutorials for the Dashboard
        $aiInterventions = LearningMaterial::where('student_id', $student->id)
                                           ->where('type', 'ai_intervention')
                                           ->orderBy('created_at', 'desc')
                                           ->get();

        return view('student.dashboard', compact('enrolledClasses', 'recentGrades', 'activeAssessments', 'aiInterventions'));
    }

    // --- AVAILABLE CLASSES ---
    public function availableClasses()
    {
        $enrolledIds = Auth::user()->enrolledClasses()->pluck('math_classes.id');
        
        $classes = MathClass::whereNotIn('id', $enrolledIds)
                    ->with('teacher')
                    ->withCount([
                        'students as approved_students_count' => function ($query) {
                            $query->where('class_student.status', 'approved');
                        }
                    ])
                    ->orderBy('class_name')
                    ->get();

        return view('student.classes', compact('classes'));
    }

    public function joinClass(Request $request)
    {
        $request->validate([
            'class_code' => 'required|string'
        ]);

        $mathClass = MathClass::where('class_code', strtoupper(trim($request->class_code)))->first();

        if (!$mathClass) {
            return back()->with('error', 'Invalid Class Code. Please check with your teacher and try again.');
        }

        $user = Auth::user();

        if ($user->enrolledClasses()->where('math_class_id', $mathClass->id)->exists()) {
            return back()->with('error', 'You have already requested to join or are enrolled in this class.');
        }

        $user->enrolledClasses()->attach($mathClass->id, ['status' => 'pending']);
        
        return back()->with('success', "Enrollment requested for {$mathClass->class_name}. Waiting for teacher approval!");
    }

    // --- PERFORMANCE & LEARNING ---
    public function grades()
    {
        $submissions = StudentSubmission::with('assessment')
                        ->where('student_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->get();
                        
        return view('student.grades', compact('submissions'));
    }

    public function learningPath()
    {
        $studentId = Auth::id();
        $aiInterventions = LearningMaterial::where('student_id', $studentId)
                                           ->where('type', 'ai_intervention')
                                           ->orderBy('created_at', 'desc')
                                           ->get();

        return view('student.learning-path', compact('aiInterventions'));
    }

    public function completeTask($id)
    {
        return back()->with('success', 'Great job! Activity marked as complete.');
    }

    // --- IN-APP QUIZ ENGINE ---
    public function takeQuiz($id)
    {
        $assessment = Assessment::with('questions')->findOrFail($id);
        return view('student.take_quiz', compact('assessment'));
    }

    public function submitQuiz(Request $request, $id)
    {
        $student = Auth::user();
        $assessment = Assessment::with('questions')->findOrFail($id);
        $mathClassId = $assessment->math_class_id;
        
        $answersSubmitted = $request->input('answers', []);
        $scratchpads = $request->input('scratchpads', []);
        $totalTime = $request->input('total_seconds_spent', 0);

        $correctCount = 0;
        $totalQuestions = $assessment->questions->count();
        $incorrectContextDetails = [];

        foreach ($assessment->questions as $q) {
            $submitted = $answersSubmitted[$q->id] ?? '';
            $studentWork = $scratchpads[$q->id] ?? 'Blank';
            
            $cleanSubmitted = trim(strtolower($submitted));
            $cleanCorrect = trim(strtolower($q->correct_answer));
            $isCorrect = false;

            if ($cleanSubmitted === $cleanCorrect) {
                $isCorrect = true;
            } 
            elseif ($q->question_type === 'computation' || $q->question_type === 'problem_solving') {
                $numSub = str_replace([',', ' '], '', $cleanSubmitted);
                $numCor = str_replace([',', ' '], '', $cleanCorrect);
                
                preg_match('/-?\d+(\.\d+)?/', $numCor, $corMatches);
                preg_match('/-?\d+(\.\d+)?/', $numSub, $subMatches);
                
                if (!empty($corMatches) && !empty($subMatches)) {
                    if ((float)$corMatches[0] === (float)$subMatches[0]) {
                        $isCorrect = true; 
                    }
                }
            }

            if ($isCorrect) {
                $correctCount++;
            } else {
                $incorrectContextDetails[] = "[Question: '{$q->question_text}' | Student Answered: '{$submitted}' | Correct Answer: '{$q->correct_answer}' | Student's Scratchpad Work: '{$studentWork}']";
            }
        }

        $finalScore = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;

        $scratchpadUsageCount = collect($scratchpads)->filter(fn($pad) => !empty(trim($pad)))->count();
        $avgTimePerItem = $totalQuestions > 0 ? round($totalTime / $totalQuestions) : 0;

        $submission = StudentSubmission::create([
            'student_id' => $student->id,
            'assessment_id' => $assessment->id,
            'answers_submitted' => $answersSubmitted,
            'scratchpad_data' => $scratchpads,
            'time_per_question' => ['avg_seconds' => $avgTimePerItem], 
            'total_time_spent' => $totalTime,
            'calculated_score' => $finalScore
        ]);

        $diagnosedWeakness = 'None Identified';
        if (count($incorrectContextDetails) > 0) {
            $geminiKey = trim(env('GEMINI_API_KEY'));
            $errorSummaryString = implode("\n", $incorrectContextDetails);
            
            $analysisPrompt = "You are a mathematical diagnostician evaluating a student's incorrect test answers. Analyze the following records, paying close attention to their 'Scratchpad Work' to understand their thought process:\n\n{$errorSummaryString}\n\nBased on these errors, pinpoint the exact mathematical learning objective they are failing. You MUST respond with ONLY ONE of the following exact categories, with no other text: 'Number and Number Sense', 'Fractions and Decimals', 'Geometry and Measurement', 'Patterns and Algebra', or 'Statistics and Probability'.";

            try {
                $geminiCall = Http::withoutVerifying()->timeout(30)->withHeaders([
                    'Content-Type' => 'application/json',
                    'x-goog-api-key' => $geminiKey
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent", [
                    'contents' => [['parts' => [['text' => $analysisPrompt]]]]
                ]);
                
                if ($geminiCall->successful()) {
                    $rawText = $geminiCall->json()['candidates'][0]['content']['parts'][0]['text'];
                    $diagnosedWeakness = trim(preg_replace('/\s+/', ' ', $rawText));
                } else {
                    $diagnosedWeakness = 'Pending Analysis';
                }
            } catch (\Exception $e) {
                $diagnosedWeakness = 'Pending Analysis';
            }
        }

        $classSubmissions = StudentSubmission::where('student_id', $student->id)
            ->whereHas('assessment', function ($query) use ($mathClassId) {
                $query->where('math_class_id', $mathClassId);
            })->get();

        $movingAverage = $classSubmissions->avg('calculated_score');
        $previousAverage = $classSubmissions->where('id', '!=', $submission->id)->avg('calculated_score') ?? $finalScore;

        $riskLevel = 'Low'; 
        try {
            $mlCall = Http::withoutVerifying()->timeout(3)->post('http://127.0.0.1:5000/predict', [
                'previous_score' => $previousAverage, 
                'current_score' => $finalScore,      
                'avg_time_per_item' => $avgTimePerItem,
                'scratchpad_usage' => $scratchpadUsageCount,
                'absences' => 0
            ]);
            
            if ($mlCall->successful()) {
                $riskLevel = $mlCall->json()['risk_level'];
            } else {
                throw new \Exception('ML Offline');
            }
        } catch (\Exception $e) {
            if ($movingAverage < 75) $riskLevel = 'Moderate';
            if ($movingAverage < 60) $riskLevel = 'High';
        }

        $student->enrolledClasses()->updateExistingPivot($mathClassId, [
            'current_risk_level' => $riskLevel,
            'diagnosed_weak_competency' => $diagnosedWeakness
        ]);

        $submission->update(['ai_weakness_diagnosis' => $diagnosedWeakness]);

        return redirect()->route('student.dashboard')->with('success', "Assessment submitted! Score: {$finalScore}%. The AI has analyzed your scratchpad work and updated your Profile.");
    }

    // --- AI INTERVENTION (REMEDIAL LESSONS) ---
    public function showIntervention($id)
    {
        $intervention = LearningMaterial::where('student_id', Auth::id())->findOrFail($id);
        return view('student.intervention', compact('intervention'));
    }

    public function submitIntervention(Request $request, $id)
    {
        $request->validate(['student_answer' => 'required|string']);
        
        $intervention = LearningMaterial::where('student_id', Auth::id())->findOrFail($id);
        $intervention->update([
            'student_answer' => $request->student_answer,
            'status' => 'completed'
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Your answers have been submitted to your teacher for review!');
    }

    // --- LINK PARENT POST-REGISTRATION ---
    public function linkParentLater(Request $request)
    {
        $request->validate([
            'parent_first_name' => ['required', 'string', 'max:255'],
            'parent_last_name' => ['required', 'string', 'max:255'],
            'parent_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'parent_phone' => ['required', 'string', 'max:20'],
        ]);

        $student = Auth::user();
        
        // Generate a random 6 digit code
        $activationCode = random_int(100000, 999999); 

        $parent = \App\Models\User::create([
            'first_name' => $request->parent_first_name,
            'last_name' => $request->parent_last_name,
            'email' => $request->parent_email,
            'password' => \Illuminate\Support\Facades\Hash::make($activationCode), // Hash the 6 digit code
            'role' => 'parent',
            'contact_number' => $request->parent_phone,
            'force_password_change' => true, // Flag them for password setup
        ]);

        $student->update([
            'parent_id' => $parent->id,
            'contact_number' => $parent->contact_number,
        ]);

        // Send the SMS
        $smsMsg = "PROGNOSMATH\nUsername: {$parent->email}\nCode: {$activationCode}\nLogin using this code to activate your parent account and set a secure password.";
        app(\App\Services\PhilSmsService::class)->sendSms($parent->contact_number, $smsMsg);

        return back()->with('success', 'Parent linked successfully! They have been sent an SMS with their activation code.');
    }
}