<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departament extends Model
{
    protected $table = 'departament';

    protected $fillable = ['name'];

    public function getParents()
    {

        $parents = collect([]);

        if ($this->parent) {
            $parent = $this->parent;
            while (!is_null($parent)) {
                if(!$parent->parent_id) break;
                
                $parents->push($parent);
                $parent = $parent->parent;
            }
            return $parents;
        }
    }

    public function parent()
    {
        return $this->belongsTo(Departament::class, 'parent_id');
    }
}
