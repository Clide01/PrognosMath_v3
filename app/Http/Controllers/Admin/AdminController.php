<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MathClass;
use App\Models\SchoolYear;
use App\Models\Competency;
use App\Models\Announcement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index()
    {
        $totalStudents = User::where('role', 'student')->count();
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalParents = User::where('role', 'parent')->count();
        $totalClasses = MathClass::count();

        $riskStats = DB::table('class_student')
            ->select('current_risk_level', DB::raw('count(*) as total'))
            ->where('status', 'approved')
            ->groupBy('current_risk_level')
            ->pluck('total', 'current_risk_level')
            ->toArray();

        $highRisk = $riskStats['High'] ?? 0;
        $modRisk = $riskStats['Moderate'] ?? 0;
        $lowRisk = $riskStats['Low'] ?? 0;
        
        $commonWeaknesses = DB::table('class_student')
            ->select('diagnosed_weak_competency', DB::raw('count(*) as total'))
            ->whereNotNull('diagnosed_weak_competency')
            ->where('diagnosed_weak_competency', '!=', 'None')
            ->groupBy('diagnosed_weak_competency')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents', 'totalTeachers', 'totalParents', 'totalClasses',
            'highRisk', 'modRisk', 'lowRisk', 'commonWeaknesses'
        ));
    }
    
    // --- GLOBAL USER MANAGEMENT (UPDATE / DELETE) ---
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'contact_number' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8', 
            'parent_id' => 'nullable', 
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
        ];

        // Standardized to contact_number
        if ($request->has('contact_number')) {
            $data['contact_number'] = $request->contact_number;
        }

        // Student-Parent Linkage & Auto-Sync
        if ($request->has('parent_id')) {
            if ($request->filled('parent_id')) {
                $data['parent_id'] = $request->parent_id;
                $parent = User::find($request->parent_id);
                if ($parent) {
                    $data['contact_number'] = $parent->contact_number;
                }
            } else {
                $data['parent_id'] = null; // Clears the parent link if left empty
            }
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // CASCADING UPDATE: Instantly update all linked children using the correct column
        if ($user->role === 'parent') {
            $newContactNumber = $data['contact_number'] ?? $user->contact_number;
            if ($newContactNumber) {
                User::where('parent_id', $user->id)->update([
                    'contact_number' => $newContactNumber
                ]);
            }
        }

        return back()->with('success', 'User account updated successfully.');
    }

    public function destroyUser($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', 'User account permanently deleted.');
    }

    // --- STUDENTS ---
    public function students()
    {
        $students = User::where('role', 'student')->orderBy('created_at', 'desc')->get();
        $parents = User::where('role', 'parent')->orderBy('last_name', 'asc')->get();
        return view('admin.students', compact('students', 'parents'));
    }

    public function storeStudent(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'parent_id' => ['nullable', 'exists:users,id'],
        ]);

        $studentData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password), 
            'role' => 'student',
        ];

        // Link Parent and Auto-Sync Parent's Mobile Number to Student upon registration
        if ($request->filled('parent_id')) {
            $parent = User::find($request->parent_id);
            if ($parent) {
                $studentData['parent_id'] = $parent->id;
                $studentData['contact_number'] = $parent->contact_number;
            }
        }

        User::create($studentData);

        return back()->with('success', 'Student account registered successfully.');
    }

    // --- TEACHERS ---
    public function teachers()
    {
        $teachers = User::where('role', 'teacher')->orderBy('created_at', 'desc')->get();
        return view('admin.teachers', compact('teachers'));
    }

    public function storeTeacher(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'contact_number' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password), 
            'role' => 'teacher',
            'contact_number' => $request->contact_number,
        ]);

        return back()->with('success', 'Teacher account registered successfully.');
    }

    // --- PARENTS ---
    public function parents()
    {
        $parents = User::where('role', 'parent')->orderBy('created_at', 'desc')->get();
        return view('admin.parents', compact('parents'));
    }

    public function storeParent(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'contact_number' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password), 
            'role' => 'parent',
            'contact_number' => $request->contact_number, 
        ]);

        return back()->with('success', 'Parent account registered successfully.');
    }

    // --- CLASS MANAGEMENT (ADMIN LEVEL) ---
    public function classes()
    {
        $classes = MathClass::with('teacher')->withCount([
            'students as approved_students_count' => function ($query) {
                $query->where('class_student.status', 'approved');
            }
        ])->orderBy('created_at', 'desc')->get();

        $teachers = User::where('role', 'teacher')->get();
        $schoolYears = SchoolYear::orderBy('year', 'desc')->get();

        return view('admin.classes', compact('classes', 'teachers', 'schoolYears'));
    }

    public function storeClass(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'class_name' => 'required|string|max:255',
            'section' => 'required|string|max:255',
            'school_year' => 'required|string|max:255',
        ]);

        $code = strtoupper(Str::random(6));
        while (MathClass::where('class_code', $code)->exists()) {
            $code = strtoupper(Str::random(6));
        }

        MathClass::create([
            'teacher_id' => $request->teacher_id,
            'class_name' => $request->class_name,
            'section' => $request->section,
            'school_year' => $request->school_year,
            'class_code' => $code,
        ]);

        return back()->with('success', 'Class created successfully. Code: ' . $code);
    }

    public function updateClass(Request $request, $id)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'class_name' => 'required|string|max:255',
            'section' => 'required|string|max:255',
            'school_year' => 'required|string|max:255',
        ]);

        $mathClass = MathClass::findOrFail($id);
        $mathClass->update($request->only('teacher_id', 'class_name', 'section', 'school_year'));

        return back()->with('success', 'Class details updated successfully.');
    }

    public function destroyClass($id)
    {
        MathClass::findOrFail($id)->delete();
        return back()->with('success', 'Class deleted successfully.');
    }

    // --- ACADEMIC SETUP ---
    public function terms()
    {
        $schoolYears = SchoolYear::orderBy('created_at', 'desc')->get();
        return view('admin.terms', compact('schoolYears'));
    }

    public function storeSchoolYear(Request $request)
    {
        $request->validate([
            'year' => 'required|string',
            'semester' => 'required|string',
        ]);
        SchoolYear::create(['year' => $request->year, 'semester' => $request->semester, 'is_active' => false]);
        return back()->with('success', 'Academic term added successfully.');
    }

    public function activateSchoolYear($id)
    {
        SchoolYear::query()->update(['is_active' => false]);
        $year = SchoolYear::findOrFail($id);
        $year->update(['is_active' => true]);
        return back()->with('success', $year->year . ' ' . $year->semester . ' is now the active term.');
    }

    public function competencies()
    {
        $competencies = Competency::orderBy('grade_level')->get();
        return view('admin.competencies', compact('competencies'));
    }

    public function storeCompetency(Request $request)
    {
        $request->validate([
            'grade_level' => 'required|string',
            'code' => 'required|string|unique:competencies',
            'description' => 'required|string',
        ]);
        Competency::create($request->all());
        return back()->with('success', 'Learning Objective saved successfully.');
    }

    public function announcements()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->get();
        return view('admin.announcements', compact('announcements'));
    }

    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_role' => 'required|in:all,teacher,student,parent',
        ]);
        Announcement::create($request->all());
        return back()->with('success', 'Platform announcement posted successfully.');
    }
}