<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'contact_number',
        'parent_id', // <-- Added this so you can attach a parent to a student
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // For Teachers: Get classes they created
    public function taughtClasses()
    {
        return $this->hasMany(MathClass::class, 'teacher_id');
    }

    // For Students: Get classes they are enrolled in or requested to join
    public function enrolledClasses()
    {
        return $this->belongsToMany(MathClass::class, 'class_student', 'student_id', 'math_class_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    // For Students: Get their grades
    public function grades()
    {
        return $this->hasMany(Grade::class, 'student_id');
    }

    // ==========================================
    // NEW: PARENT & CHILD RELATIONSHIPS
    // ==========================================

    /**
     * For Parents: Get all students (children) assigned to them.
     */
    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    /**
     * For Students: Get their assigned parent/guardian.
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }
}