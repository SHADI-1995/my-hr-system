<?php

namespace App\Models;

use App\Traits\Auditable;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'username', 'role', 'role_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use Auditable;
    use HasFactory, Notifiable;

    public function hasPermission($permissionCode)
    {
        $role = $this->role()->first();

        if (!$role) {
            return false;
        }

        return $role
            ->permissions()
            ->where('code', $permissionCode)
            ->exists();
    }

    /**
     * الموظفون التابعون لهذا المستخدم كمدير مباشر.
     * العلاقة مع employees.direct_manager_user_id.
     */
    public function managedEmployees()
    {
        return $this->hasMany(Employee::class, 'direct_manager_user_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

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
}
