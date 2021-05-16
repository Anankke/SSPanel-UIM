<?php

namespace App\Models;

use Closure;
use Illuminate\Contracts\Pagination;
use Illuminate\Database\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as EloquentMedel;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Query\Expression as QueryExpression;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\LazyCollection;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * All function below could be staticly called via this function.\
 * Return type of `$this` is actually refer to `Illuminate\Database\Eloquent\Builder`. \
 * Only `static` refer to current model.
 *
 * @version illuminate/database:v8.28.1
 *
 * @codingStandardsIgnoreStart
 * @package Illuminate\Database\Eloquent\Query
 * @method static static                            make(array $attributes = [])                                                                                        Create and return an un-saved model instance.
 * @method static $this                             withGlobalScope(string $identifier, Eloquent\Scope|Closure $scope)                                                  Register a new global scope.
 * @method static $this                             withoutGlobalScope(Eloquent\Scope|string $scope)                                                                    Remove a registered global scope.
 * @method static $this                             withoutGlobalScopes(?array $scopes = null)                                                                          Remove all or passed registered global scopes.
 * @method static array                             removedScopes()                                                                                                     Get an array of global scopes that were removed from the query.
 * @method static $this                             whereKey($id)                                                                                                       Add a where clause on the primary key to the query.
 * @method static $this                             whereKeyNot($id)                                                                                                    Add a where clause on the primary key to the query.
 * @method static $this                             where(Closure|string|array|QueryExpression $column, $operator = null, $value = null, string $boolean = 'and')       Add a basic where clause to the query.
 * @method static static                            firstWhere(Closure|string|array|QueryExpression $column, $operator = null, $value = null, string $boolean = 'and')  Add a basic where clause to the query, and return the first result.
 * @method static $this                             orWhere(Closure|array|string|QueryExpression $column, $operator = null, $value = null)                              Add an "or where" clause to the query.
 * @method static $this                             latest(string|QueryExpression $column = null)                                                                       Add an "order by" clause for a timestamp to the query.
 * @method static $this                             oldest(string|QueryExpression $column = null)                                                                       Add an "order by" clause for a timestamp to the query.
 * @method static Collection                        hydrate(array $items)                                                                                               Create a collection of models from plain arrays.
 * @method static Collection                        fromQuery(string $query, array $bindings = [])                                                                      Create a collection of models from a raw query.
 * @method static Collection|static[]|static|null   find($id, array $columns = ['*'])                                                                                   Find a model by its primary key.
 * @method static Collection                        findMany(\Illuminate\Contracts\Support\Arrayable|array $ids, array $columns = ['*'])                                Find multiple models by their primary keys.
 * @method static Collection|static|static[]        findOrFail($id, array $columns = ['*'])                                                                             Find a model by its primary key or throw an exception.
 * @method static static                            findOrNew($id, array $columns = ['*'])                                                                              Find a model by its primary key or return fresh model instance.
 * @method static static                            firstOrNew(array $attributes = [], array $values = [])                                                              Get the first record matching the attributes or instantiate it.
 * @method static static                            firstOrCreate(array $attributes = [], array $values = [])                                                           Get the first record matching the attributes or create it.
 * @method static static                            updateOrCreate(array $attributes, array $values = [])                                                               Create or update a record matching the attributes, and fill it with values.
 * @method static static                            firstOrFail(array $columns = ['*'])                                                                                 Execute the query and get the first result or throw an exception.
 * @method static static|mixed                      firstOr(Closure|array $columns = ['*'], Closure $callback = null)                                                   Execute the query and get the first result or call a callback.
 * @method static static                            sole(array|string $columns = ['*'])                                                                                 Execute the query and get the first result if it's the sole matching record.
 * @method static mixed                             value(string|QueryExpression $column)                                                                               Get a single column's value from the first result of a query.
 * @method static Collection|static[]               get(array|string $columns = ['*'])                                                                                  Execute the query as a "select" statement.
 * @method static static[]                          getModels(array|string $columns = ['*'])                                                                            Get the hydrated models without eager loading.
 * @method static array                             eagerLoadRelations(array $models)                                                                                   Eager load the relationships for the models.
 * @method static Eloquent\Relations\Relation       getRelation(string $name)                                                                                           Get the relation instance for the given relation name.
 * @method static LazyCollection                    cursor()                                                                                                            Get a lazy collection for the given query.
 * @method static SupportCollection                 pluck(string|QueryExpression $column, ?string $key = null)                                                          Get an array with the values of a given column.
 * @method static Pagination\LengthAwarePaginator   paginate(?int $perPage = null, array $columns = ['*'], string $pageName = 'page', ?int $page = null)                Paginate the given query.
 * @method static Pagination\Paginator              simplePaginate(?int $perPage = null, array $columns = ['*'], string $pageName = 'page', ?int $page = null)          Paginate the given query into a simple paginator.
 * @method static $this|static                      create(array $attributes = [])                                                                                      Save a new model and return the instance.
 * @method static $this|static                      forceCreate(array $attributes)                                                                                      Save a new model and return the instance. Allow mass-assignment.
 * @method static int                               update(array $values)                                                                                               Update records in the database.
 * @method static int                               upsert(array $values, array|string $uniqueBy, ?array $update = null)                                                Insert new records or update the existing ones.
 * @method static int                               increment(string|QueryExpression $column, float|int $amount = 1, array $extra = [])                                 Increment a column's value by a given amount.
 * @method static int                               decrement(string|QueryExpression $column, float|int $amount = 1, array $extra = [])                                 Decrement a column's value by a given amount.
 * @method static mixed                             delete()                                                                                                            Delete records from the database.
 * @method static mixed                             forceDelete()                                                                                                       Run the default delete function on the builder.\ Since we do not apply scopes here, the row will actually be deleted.
 * @method static void                              onDelete(Closure $callback)                                                                                         Register a replacement for the default delete function.
 * @method static bool                              hasNamedScope(string $scope)                                                                                        Determine if the given model has a scope.
 * @method static Builder|mixed                     scopes(array|string $scopes)                                                                                        Call the given local model scopes.
 * @method static $this                             applyScopes()                                                                                                       Apply the scopes to the Eloquent builder instance and return it.
 * @method static $this                             with(string|array $relations, string|?Closure $callback = null)                                                     Set the relationships that should be eager loaded.
 * @method static $this                             without($relations)                                                                                                 Prevent the specified relations from being eager loaded.
 * @method static static                            newModelInstance(array $attributes = [])                                                                            Create a new instance of the model being queried.
 * @method static $this                             withCasts(array $casts)                                                                                             Apply query-time casts to the model instance.
 * @method static QueryBuilder                      getQuery()                                                                                                          Get the underlying query builder instance.
 * @method static $this                             setQuery(QueryBuilder $query)                                                                                       Set the underlying query builder instance.
 * @method static QueryBuilder                      toBase()                                                                                                            Get a base query builder instance.
 * @method static array                             getEagerLoads()                                                                                                     Get the relationships being eagerly loaded.
 * @method static $this                             setEagerLoads(array $eagerLoad)                                                                                     Set the relationships being eagerly loaded.
 * @method static string                            defaultKeyName()                                                                                                    Get the default key name of the table.
 * @method static static                            getModel()                                                                                                          Get the model instance being queried.
 * @method static $this                             setModel(EloquentModel $model)                                                                                      Set a model instance for the model being queried.
 * @method static string                            qualifyColumn(string|QueryExpression $column)                                                                       Qualify the given column name by the model's table.
 * @method static Closure                           getMacro(string $name)                                                                                              Get the given macro by name.
 * @method static bool                              hasMacro(string $name)                                                                                              Checks if a macro is registered.
 *
 * @package Illuminate\Database\Eloquent\Concerns\QueriesRelationships
 * @method static $this|static      has(\Illuminate\Database\Eloquent\Relations\Relation|string $relation, string $operator = '>=', int $count = 1, string $boolean = 'and', ?Closure $callback = null) Add a relationship count / exists condition to the query.
 * @method static $this|static      orHas(string $relation, string $operator = '>=', int $count = 1)                                                                                                    Add a relationship count / exists condition to the query with an "or".
 * @method static $this|static      doesntHave(string $relation, string $boolean = 'and', ?Closure $callback = null)                                                                                    Add a relationship count / exists condition to the query.
 * @method static $this|static      orDoesntHave(string $relation)                                                                                                                                      Add a relationship count / exists condition to the query with an "or".
 * @method static $this|static      whereHas(string $relation, ?Closure $callback = null, string $operator = '>=', int $count = 1)                                                                      Add a relationship count / exists condition to the query with where clauses.
 * @method static $this|static      orWhereHas(string $relation, ?Closure $callback = null, string $operator = '>=', int $count = 1)                                                                    Add a relationship count / exists condition to the query with where clauses and an "or".
 * @method static $this|static      whereDoesntHave(string $relation, ?Closure $callback = null)                                                                                                        Add a relationship count / exists condition to the query with where clauses.
 * @method static $this|static      orWhereDoesntHave(string $relation, ?Closure $callback = null)                                                                                                      Add a relationship count / exists condition to the query with where clauses and an "or".
 * @method static $this|static      hasMorph(MorphTo|string $relation, string|array $types, string $operator = '>=', int $count = 1, string $boolean = 'and', ?Closure $callback = null)                Add a relationship count / exists condition to the query with where clauses and an "or".
 * @method static $this|static      orHasMorph(MorphTo|string $relation, string|array $types, string $operator = '>=', int $count = 1)                                                                  Add a polymorphic relationship count / exists condition to the query with an "or".
 * @method static $this|static      doesntHaveMorph(MorphTo|string $relation, string|array $types, string $boolean = 'and', ?Closure $callback = null)                                                  Add a polymorphic relationship count / exists condition to the query.
 * @method static $this|static      orDoesntHaveMorph(MorphTo|string $relation, string|array $types)                                                                                                    Add a polymorphic relationship count / exists condition to the query with an "or".
 * @method static $this|static      whereHasMorph(MorphTo|string $relation, string|array $types, ?Closure $callback = null, string $operator = '>=', int $count = 1)                                    Add a polymorphic relationship count / exists condition to the query with where clauses.
 * @method static $this|static      orWhereHasMorph(MorphTo|string $relation, string|array $types, ?Closure $callback = null, string $operator = '>=', int $count = 1)                                  Add a polymorphic relationship count / exists condition to the query with where clauses and an "or".
 * @method static $this|static      whereDoesntHaveMorph(MorphTo|string $relation, string|array $types, ?Closure $callback = null)                                                                      Add a polymorphic relationship count / exists condition to the query with where clauses.
 * @method static $this|static      orWhereDoesntHaveMorph(MorphTo|string $relation, string|array $types, ?Closure $callback = null)                                                                    Add a polymorphic relationship count / exists condition to the query with where clauses and an "or".
 * @method static $this             withAggregate($relations, string $column, string $function = null)                                                                                                  Add subselect queries to include an aggregate value for a relationship.
 * @method static $this             withCount($relations)                                                                                                                                               Add subselect queries to count the relations.
 * @method static $this             withMax(string|array $relation, string $column)                                                                                                                     Add subselect queries to include the max of the relation's column.
 * @method static $this             withMin(string|array $relation, string $column)                                                                                                                     Add subselect queries to include the min of the relation's column.
 * @method static $this             withSum(string|array $relation, string $column)                                                                                                                     Add subselect queries to include the sum of the relation's column.
 * @method static $this             withAvg(string|array $relation, string $column)                                                                                                                     Add subselect queries to include the average of the relation's column.
 * @method static $this|static      mergeConstraintsFrom(Builder $from)                                                                                                                                 Merge the where constraints from another query to the current query.
 *
 * @package Illuminate\Database\Concerns\ExplainsQueries
 * @method SupportCollection explain() Explains the query.
 *
 * @package Illuminate\Support\Traits\ForwardsCalls
 *
 * @package Illuminate\Database\Concerns\BuildsQueries
 * @method static bool              chunk(int $count, callable $callback)                                                           Chunk the results of the query.
 * @method static SupportCollection chunkMap(callable $callback, int $count = 1000)                                                 Run a map over each item while chunking.
 * @method static bool              each(callable $callback, int $count = 1000)                                                     Execute a callback over each item while chunking.
 * @method static bool              chunkById(int $count, callable $callback, ?string $column = null, ?string $alias = null)        Chunk the results of a query by comparing IDs.
 * @method static bool              eachById(callable $callback, int $count = 1000, ?string $column = null, ?string $alias = null)  Execute a callback over each item while chunking by ID.
 * @method static static|null       first(array|string $columns = ['*'])                                                            Execute the query and get the first result.
 * @method static static|null       sole(array|string $columns = ['*'])                                                             Execute the query and get the first result if it's the sole matching record.
 * @method static mixed|$this       when(mixed $value, callback $callback, ?callback $default = null)                               Apply the callback's query changes if the given "value" is true.
 * @method static $this             tap(callback $callback)                                                                         Pass the query to a given callback.
 * @method static mixed|$this       unless(mixed $value, callback $callback, ?callback $default = null)                             Apply the callback's query changes if the given "value" is false.
 * @codingStandardsIgnoreEnd
 */
class Model extends EloquentMedel
{
    public $timestamps = false;

    /**
     * 获取表名
     */
    public static function getTableName(): string
    {
        $class = get_called_class();
        return (new $class)->getTable();
    }

    /**
     * 获取表数据
     *
     * @param \Slim\Http\Request $request
     * @param callable           $callback
     * @param callable           $precondition
     *
     * @return array
     * [
     *  'datas' => \Illuminate\Database\Eloquent\Collection,
     *  'count' => int
     * ]
     */
    public static function getTableDataFromAdmin(\Slim\Http\Request $request, $callback = null, $precondition = null): array
    {
        //得到排序的方式
        $order        = $request->getParam('order')[0]['dir'];
        //得到排序字段的下标
        $order_column = $request->getParam('order')[0]['column'];
        //根据排序字段的下标得到排序字段
        $order_field  = $request->getParam('columns')[$order_column]['data'];
        if ($callback !== null) {
            call_user_func_array($callback, [&$order_field]);
        }
        $limit_start  = $request->getParam('start');
        $limit_length = $request->getParam('length');
        $search       = $request->getParam('search')['value'];

        $query = self::query();
        if ($precondition !== null) {
            call_user_func($precondition, $query);
        }
        if ($search) {
            $query->where(
                function ($query) use ($search) {
                    $query->where('id', 'LIKE binary', "%$search%");
                    $attributes = Capsule::schema()->getColumnListing(self::getTableName());
                    foreach ($attributes as $s) {
                        if ($s != 'id') {
                            $query->orwhere($s, 'LIKE binary', "%$search%");
                        }
                    }
                }
            );
        }
        return [
            'count' => (clone $query)->count(),
            'datas' => $query->orderByRaw($order_field . ' ' . $order)->skip($limit_start)->limit($limit_length)->get(),
        ];
    }
}
