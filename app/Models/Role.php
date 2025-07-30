<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;


class Role extends SpatieRole
{

        
    public function superior()
    {
        return $this->hasOneThrough(
            Role::class,            // The final model (the superior role)
            RoleRelationship::class, // The intermediate model (pivot table)
            'subordinate_role_id',  // Foreign key on the role_relationships table pointing to the subordinate
            'id',                   // Foreign key on the roles table (superior role's ID)
            'id',                   // Local key on the roles table (subordinate role's ID)
            'superior_role_id'      // Local key on the role_relationships table (the superior's ID)
        );
    }

    // Relationship to get all subordinates of a role (many subordinates)
    public function subordinates()
    {
        return $this->hasManyThrough(
            Role::class,            // The final model (the subordinate role)
            RoleRelationship::class, // The intermediate model (pivot table)
            'superior_role_id',     // Foreign key on the role_relationships table pointing to the superior
            'id',                   // Foreign key on the roles table (subordinate role's ID)
            'id',                   // Local key on the roles table (superior role's ID)
            'subordinate_role_id'   // Local key on the role_relationships table (the subordinate's ID)
        );
    }

    // Method to get all subordinates recursively (for a role)
    public function allSubordinates($subordinates = [])
    {
        $directSubordinates = $this->subordinates()->get();  // Fetch immediate subordinates
        foreach ($directSubordinates as $subordinate) {
            $subordinates[] = $subordinate->name;  // Add the subordinate's name
            // Flatten all nested subordinates into the same array
            $subordinates = $subordinate->allSubordinates($subordinates);
        }
        return $subordinates;
    }
    // Method to get all superiors recursively (for a role)
    public function allSuperiors($superiors = [])
    {
        $directSuperior = $this->superior()->first();  // Get the immediate superior

        if ($directSuperior) {
            $superiors[] = $directSuperior;  // Add the superior to the list
            $directSuperior->allSuperiors($superiors);  // Recursively add the superior's superiors
        }

        return $superiors;
    }
}
