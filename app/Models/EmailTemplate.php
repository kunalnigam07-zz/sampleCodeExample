<?php

namespace App\Models;

class EmailTemplate extends BaseModel
{
	protected $table = 'email_templates';
    protected $fillable = ['title', 'html_version', 'text_version', 'sms', 'from_name', 'from_email', 'subject'];
}
