<?php

namespace App\Application\Service;

use App\Entity\Setting;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;

class SettingService
{
    /**
     * @var array
     */
    private $loadedSettings = [];

    private $settingsLoaded = false;

    private $entityManager;

    private $settingRepository;


    public function __construct( EntityManagerInterface $entityManager,  SettingRepository $settingRepository) {

        $this->entityManager = $entityManager;
        $this->settingRepository = $settingRepository;
    }

    /**
     * @param string $identifier

     * @param mixed|null $default
     *
     * @return mixed
     */
    public function get(string $identifier, $default = null)
    {
        if (false === $this->has($identifier)) {
            return $default;
        }

        return $this->loadedSettings[$identifier];
    }

    /**
     * @param string $identifier
     * @param mixed $value
     */
    public function set(string $identifier, $value): void
    {
        $setting = $this->settingRepository->findOneBy(['identifier' => $identifier]);
        if (null === $setting) {
            $setting = new Setting($identifier, $value);
        }

        $this->entityManager->persist($setting);
        $this->entityManager->flush();

        $this->loadedSettings[$identifier] = $value;
    }

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function has(string $identifier): bool
    {
        $this->loadSettings();

        return isset($this->loadedSettings[$identifier]);
    }

    /**
     * Loads all settings into memory
     */
    private function loadSettings(): void
    {
        if (true === $this->settingsLoaded) {
            return;
        }

        $settings = $this->settingRepository->findAll();
        foreach($settings as $setting) {
            $this->loadedSettings[$setting->getIdentifier()] = $setting->getValue();
        }

        $this->settingsLoaded = true;
    }
}