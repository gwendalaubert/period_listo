<?php

namespace App\Constants;

class Constants
{
    const CODE_SUCCESS              = "000";
    const CODE_ERROR                = "100";
    const CODE_REGEX_NOT_MATCH      = "101";

    const PATTERN_RFC3339 = '/^((?:(\d{4}-\d{2}-\d{2})T(\d{2}:\d{2}:\d{2}(?:\.\d+)?))(Z|[\+-]\d{2}:\d{2})?)$/';
}