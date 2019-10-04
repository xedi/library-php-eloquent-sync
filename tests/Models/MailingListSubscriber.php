<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Tests\Models\MailingList;

class MailingListSubscriber extends Model
{
    protected $fillable = [
        'mailing_list_id',
        'email_address',
    ];

    public function mailingList()
    {
        return $this->belongsTo(
            MailingList::class,
            'mailing_list_id'
        );
    }
}
