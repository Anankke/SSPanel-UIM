<?php

namespace App\Models;

/**
 * Ann Model
 *
 * @property-read   int    $id         Announcement ID
 * @property        string $date       Date announcement posted
 * @property        string $content    Announcement in HTML
 * @property        string $markdown   Announcement in MarkDown
 */
class Ann extends Model
{
    protected $connection = 'default';
    protected $table = 'announcement';
}
