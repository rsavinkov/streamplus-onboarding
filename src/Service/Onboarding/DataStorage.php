<?php

declare(strict_types=1);

namespace App\Service\Onboarding;

interface DataStorage
{
    public function getAllData(): array;
    public function getData(string $key): mixed;
    public function saveData($data): void;
    public function clearData(): void;
}