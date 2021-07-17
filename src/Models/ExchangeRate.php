<?php
namespace Jalno\LaravelMoney\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    /**
     * The relations to eager load on every query.
     *
     * @var string[]
     */
    protected static array $withRelations = [];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'laravelmoney_exchange_rates';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'base_currency_id',
		'quote_currency_id',
		'ratio',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [];

    /**
     * The model's default values for attributes.
     *
     * @var array<string,mixed>
     */
    protected $attributes = [];

    /**
     * The relations to eager load on every query.
     *
     * @var string[]
     */
    protected $with = [];

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array<string,mixed>  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->with = array_merge($this->with, self::$withRelations);
    }

}
