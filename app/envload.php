<?php

// make replace _ENV with env
function searchEnvName($name)
{
    global $_ENV;
    foreach ($_ENV as $configKey => $configValue) {
        if (strtoupper($configKey) === $name) {
            return $configKey;
        }
    }
    return null;
}

if (getenv('UIM_ENV_REPLACE_ENABLE')) {
    foreach (getenv() as $envKey => $envValue) {
        global $_ENV;
        $envUpKey = strtoupper($envKey);
        // Key starts with UIM_
        if (strpos($envUpKey, "UIM_") === 0) {
            // Valid env key, set to _ENV
            $configKey = substr($envUpKey, 4);
            $realKey = searchEnvName($configKey);
            if ($realKey !== null) {
                $_ENV[$realKey] = $envValue;
            }
        }
    }
}
