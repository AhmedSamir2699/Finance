<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleRelationship extends Model
{
    protected $table = 'role_relationships';
    protected $fillable = ['superior_role_id', 'subordinate_role_id'];

    public function superiorRole()
    {
        return $this->belongsTo(Role::class, 'superior_role_id');
    }

    public function subordinateRole()
    {
        return $this->belongsTo(Role::class, 'subordinate_role_id');
    }
}
