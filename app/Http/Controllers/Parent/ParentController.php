<?php
namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\User;
use App\Models\StudentSubmission;
use App\Models\LearningMaterial;

class ParentController extends Controller
{
    public function dashboard()
    {
        $parent = Auth::user();
        
        // Fetch all students linked to this parent. 
        $children = $parent->children ?? collect(); 
        
        return view('parent.dashboard', compact('children'));
    }

    public function childAnalytics($id)
    {
        // 1. Fetch the specific child profile
        $child = User::findOrFail($id);

        // 2. Fetch all test submissions for this child
        $submissions = StudentSubmission::with('assessment')
            ->where('student_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. Fetch all AI remedial modules assigned to this child
        $interventions = LearningMaterial::where('student_id', $id)
            ->where('type', 'ai_intervention')
            ->latest()
            ->get();

        return view('parent.child_analytics', compact('child', 'submissions', 'interventions'));
    }
}