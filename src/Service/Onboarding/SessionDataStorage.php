<?php

namespace App\Service\Onboarding;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionDataStorage implements DataStorage
{
    private const SESSION_KEY = 'onboarding_data';

    public function __construct(
        private readonly SessionInterface $session,
    ) {
        $this->init();
    }

    private function init(): void
    {
        if (!$this->session->has(self::SESSION_KEY)) {
            $this->session->set(self::SESSION_KEY, []);
        }
    }

    public function getAllData(): array
    {
        return $this->session->get(self::SESSION_KEY, []);
    }

    public function getData(string $key): mixed
    {
        $allData = $this->getAllData();

        return $allData[$key] ?? null;
    }

    public function saveData($data): void
    {
        $this->session->set(self::SESSION_KEY, array_merge($this->getAllData(), $data));
    }

    public function clearData(): void
    {
        $this->session->remove(self::SESSION_KEY);
    }
}