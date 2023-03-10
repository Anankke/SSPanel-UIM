<?php

declare(strict_types=1);

if (getenv('UIM_ENV_REPLACE_ENABLE')) {
    foreach (getenv() as $envKey => $envValue) {
        global $_ENV;
        $envUpKey = strtoupper($envKey);
        // Key starts with UIM_
        if (str_starts_with($envUpKey, 'UIM_')) {
            // Valid env key, set to _ENV
            $configKey = substr($envUpKey, 4);
            $realKey = App\Utils\Tools::searchEnvName($configKey);
            if ($realKey !== null) {
                $_ENV[$realKey] = $envValue;
            }
        }
    }
}
