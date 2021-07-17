<?php

namespace Jalno\LaravelMoney\Models;

use Jalno\Money\Contracts\ICurrency;
use Jalno\Translator\Models\Translate;
use Jalno\Money\Contracts\ICurrency\Expression;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property positive-int $id
 * @property string $code
 * @property positive-int $rounding_mode
 * @property int $rounding_precesion
 */
class Currency extends Model implements ICurrency
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleted(function (Currency $currency) {
            Translate::where("table", "laravelmoney_currencies.title")
                ->where("pk", $currency->id)
                ->delete();
            Translate::where("table", "laravelmoney_currencies.prefix")
                ->where("pk", $currency->id)
                ->delete();
            Translate::where("table", "laravelmoney_currencies.postfix")
                ->where("pk", $currency->id)
                ->delete();
        });
    }

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
    protected $table = 'laravelmoney_currencies';

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
        'rounding_mode',
        'rounding_precision',
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
    protected $with = ["titles", "prefixes", "postfixes"];

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array<string,mixed>  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        Relation::morphMap([
            'laravelmoney_currencies.title' => __NAMESPACE__ . "\Currency",
        ]);
        Relation::morphMap([
            'laravelmoney_currencies.prefix' => __NAMESPACE__ . "\Currency",
        ]);
        Relation::morphMap([
            'laravelmoney_currencies.postfix' => __NAMESPACE__ . "\Currency",
        ]);
        parent::__construct($attributes);
    }

    public function prefixes(): MorphMany
    {
        return $this->morphMany(Translate::class, 'parentable', 'table', 'pk');
    }

    public function titles(): MorphMany
    {
        return $this->morphMany(Translate::class, 'parentable', 'table', 'pk');
    }

    public function postfixes(): MorphMany
    {
        return $this->morphMany(Translate::class, 'parentable', 'table', 'pk');
    }

    /**
     * @return positive-int
     */
    public function getID(): int
    {
        return $this->id;
    }

    /**
     * @param positive-int $id
     */
    public function setID(int $id): void
    {
        $this->id = $id;
    }

    public function getCode(): string
    {
        return strtoupper($this->code);
    }


    public function setCode(string $code): void
    {
        $this->code = strtoupper($code);
    }

    public function getPrefix(string $lang, ?string $locale = null): ?Expression
    {
        $item = $this->prefixes->first(fn($prefix) => $prefix->lang == $lang);
        return $item ?
            new Expression($item->text, $item->lang, null) :
            null;
    }

    public function getPrefixes(): array
    {
        return array_map(
            fn(Translate $prefix) => new Expression($prefix->text, $prefix->lang, null),
            $this->postfixes->all()
        );
    }

    public function setPrefix(Expression $prefix): void
    {
        $translate = Translate::where("table", $this->table . ".prefix")
                    ->where("pk", $this->id)
                    ->where("lang", $prefix->getLanguage())
                    ->first();

        if (!$translate) {
            $translate = new Translate();
            $translate->table = $this->table . ".prefix";
            $translate->pk = $this->id;
            $translate->lang = $prefix->getLanguage();
        }
        $translate->text = $prefix->getValue();
        $translate->saveOrFail();
    }

    public function getTitle(string $lang, ?string $locale = null): ?Expression
    {
        $item = $this->titles->first(fn($title) => $title->lang == $lang);
        return $item ?
            new Expression($item->text, $item->lang, null) :
            null;
    }

    public function setTitle(Expression $title): void
    {
        $translate = Translate::where("table", $this->table . ".title")
                    ->where("pk", $this->id)
                    ->where("lang", $title->getLanguage())
                    ->first();

        if (!$translate) {
            $translate = new Translate();
            $translate->table = $this->table . ".title";
            $translate->pk = $this->id;
            $translate->lang = $title->getLanguage();
        }
        $translate->text = $title->getValue();
        $translate->saveOrFail();
    }

    public function setPostfix(Expression $postfix): void
    {
        $translate = Translate::where("table", $this->table . ".prefix")
                    ->where("pk", $this->id)
                    ->where("lang", $postfix->getLanguage())
                    ->first();

        if (!$translate) {
            $translate = new Translate();
            $translate->table = $this->table . ".prefix";
            $translate->pk = $this->id;
            $translate->lang = $postfix->getLanguage();
        }
        $translate->text = $postfix->getValue();
        $translate->saveOrFail();
    }

	public function getPostfix(string $lang, ?string $locale = null): ?Expression {
        $item = $this->postfixes->first(fn($postfix) => $postfix->lang == $lang);
        return $item ?
            new Expression($item->text, $item->lang, null) :
            null;
    }

    public function getPostfixes(): array
    {
        return array_map(
            fn(Translate $postfix) => new Expression($postfix->text, $postfix->lang, null),
            $this->postfixes->all()
        );
    }

    /**
     * @param positive-int $mode
     */
    public function setRoundingMode(int $mode): void
    {
        $this->rounding_mode = $mode;
    }

    public function getRoundingMode(): int
    {
        return $this->rounding_mode;
    }

    public function setRoundingPrecision(int $precesion): void
    {
        $this->rounding_precesion = $precesion;
    }

	public function getRoundingPrecision(): int {
        return $this->rounding_precesion;
    }

}
