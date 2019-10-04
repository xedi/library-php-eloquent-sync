<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Tests\Models\MailingListSubscriber;

class MailingList extends Model
{
    protected $fillable = [ 'name' ];

    protected $connection = 'sqlite';

    public function subscribers()
    {
        return $this->hasMany(
            MailingListSubscriber::class,
            'mailing_list_id'
        );
    }
}
