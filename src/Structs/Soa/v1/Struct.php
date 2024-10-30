<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Exceptions\Structs\v1\StructSaveException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

/**
 * Class Struct.
 */
abstract class Struct extends Model
{
    /**
     * The data type of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Set of validation rules used on Struct.
     *
     * @see ValidateModel
     *
     * @var array<string,mixed>
     */
    protected array $rules = [];

    /**
     * return void.
     */
    public function validate(): void
    {
        Validator::validate(
            $this->toArray(),
            $this->validationRules(),
        );
    }

    /**
     * The "booted" method of the model.
     *
     * Used to throw `StructSaveException` if someone try to save a struct.
     *
     * @see StructSaveException
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::saving(
            static function () {
                throw new StructSaveException();
            }
        );
    }

    /**
     * Validation rules for request data.
     *
     * @return array<string, mixed>
     */
    protected function validationRules(): array
    {
        return [
            ...$this->rules,
        ];
    }
}
