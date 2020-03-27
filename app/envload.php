<?php

// make replace _ENV with env
function searchEnvName($name)
{
    global $_ENV;
    foreach ($_ENV as $configKey => $configValue) {
        if (strtoupper($configKey) == $name) {
            return $configKey;
        }
    }

    return NULL;
}

if (getenv('UIM_ENV_REPLACE_ENABLE')) {
    foreach (getenv() as $envKey => $envValue) {
        global $_ENV;
        $envUpKey = strtoupper($envKey);
        // Key starts with UIM_
        if (substr($envUpKey, 0, 4) == "UIM_") {
            // Vaild env key, set to _ENV
            $configKey = substr($envUpKey, 4);
            $realKey = searchEnvName($configKey);
            if ($realKey != NULL) {
                $_ENV[$realKey] = $envValue;
            }
        }
    }
}
