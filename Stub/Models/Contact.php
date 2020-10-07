<?php


namespace Sourcegr\Stub\Models;


use Sourcegr\Framework\Database\Freakquent\BaseModel;
use Sourcegr\Framework\Database\Freakquent\Relations\RelationTrait;

class Contact extends BaseModel
{
    use RelationTrait;

    protected static $table = 'contacts';

    /**
     * @var int $id
     */
    public $id;
    public $name;
    public $company_id;


    public function company() {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

}