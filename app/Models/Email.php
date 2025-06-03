<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $table = 'emails';

    protected $fillable = [
        'to', 'cc', 'bcc', 'subject',
        'body', 'attachments', 'status', 'send_at',
        'error_message',
    ];
}