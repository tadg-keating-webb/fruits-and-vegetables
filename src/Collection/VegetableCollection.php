<?php

namespace App\Collection;

use App\DTO\ProduceDTO;
use App\Enum\ProduceEnum;
use App\Storage\DatabaseStorage;
use InvalidArgumentException;

class VegetableCollection implements CollectionInterface
{
    private array $vegetables;

    public function __construct(private DatabaseStorage $storage) 
    {
        $this->vegetables = $this->storage->list(ProduceEnum::VEGETABLE->value);    
    }
    
    public function add(string $item, int $grams): void
    {
        $this->storage->add(ProduceEnum::VEGETABLE->value, ['name' => $item, 'weight' => $grams]);

        $this->vegetables[] = new ProduceDTO($item, $grams);
    }

    public function remove(string $item): bool
    {
        try { 
            $this->storage->remove(ProduceEnum::VEGETABLE->value, $item);
        } catch (InvalidArgumentException $e) {
            return false;
        }
        
        $this->vegetables = array_filter($this->vegetables, fn(ProduceDTO $vegetableDTO) => $vegetableDTO->name !== $item);
        return true;
    }

    public function list(array $filters = []): array
    {
        if ($filters) {
            return $this->storage->list(ProduceEnum::VEGETABLE->value, $filters);
        }

        return $this->vegetables;
    }

    public function search(string $filter): array
    {
        return $this->list(['name' => $filter]);
    }
}
