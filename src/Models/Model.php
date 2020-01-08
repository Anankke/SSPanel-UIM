<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentMedel;
use Illuminate\Database\Eloquent;

/**
 * Base Model
 *
 * @codingStandardsIgnoreStart
 * @method Eloquent\Model       make(array $attributes = []) Create and return an un-saved model instance.
 * @method static               whereKey($id) Add a where clause on the primary key to the query.
 * @method static               whereKeyNot($id) Add a where clause on the primary key to the query.
 * @method static               where($column, $operator = null, $value = null, $boolean = 'and') Add a basic where clause to the query.
 * @method static               orWhere($column, $operator = null, $value = null) Add an "or where" clause to the query.
 * @method static               latest($column = null) Add an "order by" clause for a timestamp to the query.
 * @method static               oldest($column = null) Add an "order by" clause for a timestamp to the query.
 * @method Eloquent\Collection  hydrate(array $items) Create a collection of models from plain arrays.
 * @method Eloquent\Collection  fromQuery($query, $bindings = []) Create a collection of models from a raw query.
 * @method Eloquent\Model|Eloquent\Collection|static[]|static|null find($id, $columns = ['*']) Find a model by its primary key.
 * @method Eloquent\Collection  findMany($ids, $columns = ['*']) Find multiple models by their primary keys.
 * @method Eloquent\Model|Eloquent\Collection|static|static[] findOrFail($id, $columns = ['*']) Find a model by its primary key or throw an exception.
 * @method Eloquent\Model|static findOrNew($id, $columns = ['*']) Find a model by its primary key or return fresh model instance.
 * @method Eloquent\Model|object|static|null first($columns = ['*']) Execute the query and get the first result.
 * @method Eloquent\Model|static firstOrNew(array $attributes, array $values = []) Get the first record matching the attributes or instantiate it.
 * @method Eloquent\Model|static firstOrCreate(array $attributes, array $values = []) Get the first record matching the attributes or create it.
 * @method Eloquent\Model|static updateOrCreate(array $attributes, array $values = []) Create or update a record matching the attributes, and fill it with values.
 * @method Eloquent\Model|static firstOrFail($columns = ['*']) Execute the query and get the first result or throw an exception.
 * @method Eloquent\Model|static|mixed firstOr($columns = ['*'], Closure $callback = null) Execute the query and get the first result or call a callback.
 * @method mixed                value($column) Get a single column's value from the first result of a query.
 * @method Eloquent\Collection|static get($columns = ['*']) Execute the query as a "select" statement.
 * @method Eloquent\Model[]|static getModels($columns = ['*']) Get the hydrated models without eager loading.
 * @method \Illuminate\Support\LazyCollection cursor() Get a lazy collection for the given query.
 * @method \Illuminate\Support\Collection pluck($column, $key = null) Get an array with the values of a given column.
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null) Paginate the given query.
 * @method \Illuminate\Contracts\Pagination\Paginator simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null) Paginate the given query into a simple paginator.
 * @method Eloquent\Model|static create(array $attributes = []) Save a new model and return the instance.
 * @method Eloquent\Model|static forceCreate(array $attributes) Save a new model and return the instance. Allow mass-assignment.
 * @method int                  update(array $values) Update a record in the database.
 * @method int                  increment($column, $amount = 1, array $extra = []) Increment a column's value by a given amount.
 * @method int                  decrement($column, $amount = 1, array $extra = []) Decrement a column's value by a given amount.
 * @method mixed                delete() Delete a record from the database.
 * @method mixed                forceDelete() Run the default delete function on the builder.
 * @method void                 onDelete(Closure $callback) Register a replacement for the default delete function.
 * @method static|mixed scopes($scopes) Call the given local model scopes.
 * @method static               applyScopes() Apply the scopes to the Eloquent builder instance and return it.
 * @codingStandardsIgnoreEnd
 * @mixin Eloquent\Builder
 */
class Model extends EloquentMedel
{
    public $timestamps = false;
}
