<?php

namespace App\Models;

class Block extends BaseModel
{
	protected $table = 'blocks';
    protected $fillable = ['title', 'contents'];
}
