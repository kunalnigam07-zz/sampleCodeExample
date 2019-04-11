<?php

namespace App\Models;

class Import extends BaseModel
{
	protected $table = 'imports';
    protected $fillable = ['contents', 'user_id', 'import_options'];
}
