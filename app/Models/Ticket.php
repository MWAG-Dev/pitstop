<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
	public const CATEGORY_PRIORITY_MAP = [
		'General' => 'Normal',
		'Phone System' => 'High',
		'Website' => 'High',
		'Onboarding' => 'Normal',
		'Off-boarding' => 'Normal',
		'Internet' => 'Critical',
		'Other' => 'Low',
	];

    protected $fillable = [
        'requester_email',
	'subject',
	'description',
	'category',
	'priority',
	'status',
    ];

    public function replies()
    {
	return $this->hasMany(\App\Models\TicketReply::class)
		->orderBy('created_at');
    }

}
