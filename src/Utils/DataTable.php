<?php

declare(strict_types=1);

namespace App\Utils;

use Illuminate\Database\Eloquent\Builder;
use Slim\Http\ServerRequest;
use function filter_var;
use function in_array;
use function is_array;
use function is_string;
use function strtolower;
use const FILTER_VALIDATE_INT;

final class DataTable
{
    /**
     * Apply the ordering of a DataTables server-side request to the query.
     *
     * The column index sent by the client is resolved against $columns (the table's column keys in display order).
     * Only keys listed in $sortable are used as SQL columns; any other index, including computed display fields,
     * falls back to ordering by $default. A column other than $default is followed by $default desc as tie-breaker.
     *
     * @param array<int, string> $columns
     * @param array<int, string> $sortable
     */
    public static function applyOrder(
        Builder $query,
        ServerRequest $request,
        array $columns,
        array $sortable,
        string $default
    ): void {
        $order = $request->getParam('order')[0] ?? [];
        $order = is_array($order) ? $order : [];

        $dir = is_string($order['dir'] ?? null) && strtolower($order['dir']) === 'asc' ? 'asc' : 'desc';
        $index = filter_var($order['column'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        $column = $index === false ? null : ($columns[$index] ?? null);

        if ($column === null || $column === $default || ! in_array($column, $sortable, true)) {
            $query->orderBy($default, $dir);

            return;
        }

        $query->orderBy($column, $dir)->orderBy($default, 'desc');
    }
}
