<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Database\Factories\v1;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\Enum\Laravel\Faker\FakerEnumProvider;

/**
 * Abstract class AbstractFactory.
 *
 * @template TModel of Model
 * @extends Factory<TModel>
 */
abstract class AbstractFactory extends Factory
{
    /**
     * @param int|null $count
     * @param Collection|null $states
     * @param Collection|null $has
     * @param Collection|null $for
     * @param Collection|null $afterMaking
     * @param Collection|null $afterCreating
     * @param string|null $connection
     * @param Collection|null $recycle
     */
    public function __construct(?int $count = null, ?Collection $states = null, ?Collection $has = null, ?Collection $for = null, ?Collection $afterMaking = null, ?Collection $afterCreating = null, ?string $connection = null, ?Collection $recycle = null)
    {
        parent::__construct($count, $states, $has, $for, $afterMaking, $afterCreating, $connection, $recycle);

        FakerEnumProvider::register();
    }
}
