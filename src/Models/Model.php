<?php

declare(strict_types=1);

namespace App\Models;

use Closure;
use DateTimeInterface;
use Illuminate\Contracts\Pagination\Cursor;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\LazyCollection;

/**
 * Eloquent Model
 *
 * @version illuminate/database:v9.17.0
 *
 * @codingStandardsIgnoreStart
 *
 * @see \Illuminate\Database\Eloquent\Builder
 *
 * @method static static                            make(array $attributes = [])                                                                                    Create and return an un-saved model instance.
 * @method static $this                             withGlobalScope(string $identifier, Scope|Closure $scope)                                                       Register a new global scope.
 * @method static $this                             withoutGlobalScope(Scope|string $scope)                                                                         Remove a registered global scope.
 * @method static $this                             withoutGlobalScopes(?array $scopes = null)                                                                      Remove all or passed registered global scopes.
 * @method static array                             removedScopes()                                                                                                 Get an array of global scopes that were removed from the query.
 * @method static $this                             whereKey($id)                                                                                                   Add a where clause on the primary key to the query.
 * @method static $this                             whereKeyNot($id)                                                                                                Add a where clause on the primary key to the query.
 * @method static $this                             where(Closure|string|array|Expression $column, $operator = null, $value = null, string $boolean = 'and')        Add a basic where clause to the query.
 * @method static static                            firstWhere(Closure|string|array|Expression $column, $operator = null, $value = null, string $boolean = 'and')   Add a basic where clause to the query, and return the first result.
 * @method static $this                             orWhere(Closure|array|string|Expression $column, $operator = null, $value = null)                               Add an "or where" clause to the query.
 * @method static $this                             whereNot(Closure|array|string|Expression $column, $operator = null, $value = null, string $boolean = 'and')     Add a basic "where not" clause to the query.
 * @method static $this                             orWhereNot(Closure|array|string|Expression $column, $operator = null, $value = null)                            Add an "or where not" clause to the query.
 * @method static $this                             latest(string|Expression $column = null)                                                                        Add an "order by" clause for a timestamp to the query.
 * @method static $this                             oldest(string|Expression $column = null)                                                                        Add an "order by" clause for a timestamp to the query.
 * @method static Collection                        hydrate(array $items)                                                                                           Create a collection of models from plain arrays.
 * @method static Collection                        fromQuery(string $query, array $bindings = [])                                                                  Create a collection of models from a raw query.
 * @method static Collection|static[]|static|null   find($id, array $columns = ['*'])                                                                               Find a model by its primary key.
 * @method static Collection                        findMany(Arrayable|array $ids, array $columns = ['*'])                                                          Find multiple models by their primary keys.
 * @method static Collection|static|static[]        findOrFail($id, array $columns = ['*'])                                                                         Find a model by its primary key or throw an exception.
 * @method static static                            findOrNew($id, array $columns = ['*'])                                                                          Find a model by its primary key or return fresh model instance.
 * @method static Collection|static|static[]|mixed  findOr($id, Closure|array|string $columns = ['*'], Closure $callback = null)                                    Find a model by its primary key or call a callback.
 * @method static static                            firstOrNew(array $attributes = [], array $values = [])                                                          Get the first record matching the attributes or instantiate it.
 * @method static static                            firstOrCreate(array $attributes = [], array $values = [])                                                       Get the first record matching the attributes or create it.
 * @method static static                            updateOrCreate(array $attributes, array $values = [])                                                           Create or update a record matching the attributes, and fill it with values.
 * @method static static                            firstOrFail(array $columns = ['*'])                                                                             Execute the query and get the first result or throw an exception.
 * @method static static|mixed                      firstOr(Closure|array $columns = ['*'], Closure $callback = null)                                               Execute the query and get the first result or call a callback.
 * @method static static                            sole(array|string $columns = ['*'])                                                                             Execute the query and get the first result if it's the sole matching record.
 * @method static mixed                             value(string|Expression $column)                                                                                Get a single column's value from the first result of a query.
 * @method static mixed                             soleValue(string|Expression $column)                                                                            Get a single column's value from the first result of a query if it's the sole matching record.
 * @method static mixed                             valueOrFail(string|Expression $column)                                                                          Get a single column's value from the first result of the query or throw an exception.
 * @method static Collection|static[]               get(array|string $columns = ['*'])                                                                              Execute the query as a "select" statement.
 * @method static static[]                          getModels(array|string $columns = ['*'])                                                                        Get the hydrated models without eager loading.
 * @method static array                             eagerLoadRelations(array $models)                                                                               Eager load the relationships for the models.
 * @method static Eloquent\Relations\Relation       getRelation(string $name)                                                                                       Get the relation instance for the given relation name.
 * @method static LazyCollection                    cursor()                                                                                                        Get a lazy collection for the given query.
 * @method static SupportCollection                 pluck(string|Expression $column, ?string $key = null)                                                           Get an array with the values of a given column.
 * @method static LengthAwarePaginator              paginate(?int $perPage = null, array $columns = ['*'], string $pageName = 'page', ?int $page = null)            Paginate the given query.
 * @method static Paginator                         simplePaginate(?int $perPage = null, array $columns = ['*'], string $pageName = 'page', ?int $page = null)      Paginate the given query into a simple paginator.
 * @method static CursorPaginator                   cursorPaginate(?int $perPage = null, array|string $columns = ['*'], string $cursorName = 'cursor', Cursor|string|null $cursor = null)   Paginate the given query into a cursor paginator.
 * @method static $this|static                      create(array $attributes = [])                                                  Save a new model and return the instance.
 * @method static $this|static                      forceCreate(array $attributes)                                                  Save a new model and return the instance. Allow mass-assignment.
 * @method static int                               update(array $values)                                                           Update records in the database.
 * @method static int                               upsert(array $values, array|string $uniqueBy, ?array $update = null)            Insert new records or update the existing ones.
 * @method static int                               increment(string|Expression $column, float|int $amount = 1, array $extra = [])  Increment a column's value by a given amount.
 * @method static int                               decrement(string|Expression $column, float|int $amount = 1, array $extra = [])  Decrement a column's value by a given amount.
 * @method static mixed                             delete()                                                                        Delete records from the database.
 * @method static mixed                             forceDelete()                                                                   Run the default delete function on the builder.\ Since we do not apply scopes here, the row will actually be deleted.
 * @method static void                              onDelete(Closure $callback)                                                     Register a replacement for the default delete function.
 * @method static bool                              hasNamedScope(string $scope)                                                    Determine if the given model has a scope.
 * @method static Builder|mixed                     scopes(array|string $scopes)                                                    Call the given local model scopes.
 * @method static $this                             applyScopes()                                                                   Apply the scopes to the Eloquent builder instance and return it.
 * @method static $this                             with(string|array $relations, string|?Closure $callback = null)                 Set the relationships that should be eager loaded.
 * @method static $this                             without($relations)                                                             Prevent the specified relations from being eager loaded.
 * @method static $this                             withOnly($relations)                                                            Set the relationships that should be eager loaded while removing any previously added eager loading specifications.
 * @method static static                            newModelInstance(array $attributes = [])                                        Create a new instance of the model being queried.
 * @method static $this                             withCasts(array $casts)                                                         Apply query-time casts to the model instance.
 * @method static QueryBuilder                      getQuery()                                                                      Get the underlying query builder instance.
 * @method static $this                             setQuery(QueryBuilder $query)                                                   Set the underlying query builder instance.
 * @method static QueryBuilder                      toBase()                                                                        Get a base query builder instance.
 * @method static array                             getEagerLoads()                                                                 Get the relationships being eagerly loaded.
 * @method static $this                             setEagerLoads(array $eagerLoad)                                                 Set the relationships being eagerly loaded.
 * @method static $this                             withoutEagerLoad(array $relations)                                              Indicate that the given relationships should not be eagerly loaded.
 * @method static $this                             withoutEagerLoads()                                                             Flush the relationships being eagerly loaded.
 * @method static string                            defaultKeyName()                                                                Get the default key name of the table.
 * @method static static                            getModel()                                                                      Get the model instance being queried.
 * @method static $this                             setModel(EloquentModel $model)                                                  Set a model instance for the model being queried.
 * @method static string                            qualifyColumn(string|Expression $column)                                        Qualify the given column name by the model's table.
 * @method static Closure                           getMacro(string $name)                                                          Get the given macro by name.
 * @method static bool                              hasMacro(string $name)                                                          Checks if a macro is registered.
 * @method static Closure                           getGlobalMacro(string $name)                                                    Get the given global macro by name.
 * @method static bool                              hasGlobalMacro($name)                                                           Checks if a global macro is registered.
 *
 * functions from \Illuminate\Database\Query\Builder which could be called staticly
 * @method static mixed                 aggregate(string $function, array $columns = ['*'])                 Execute an aggregate function on the database.
 * @method static mixed                 average(string $column)                                             Alias for the "avg" method.
 * @method static mixed                 avg(string $column)                                                 Retrieve the average of the values of a given column.
 * @method static int                   count(string $columns = '*')                                        Retrieve the "count" result of the query.
 * @method static void                  dd()                                                                Die and dump the current SQL and bindings.
 * @method static bool                  doesntExist()                                                       Determine if no rows exist for the current query.
 * @method static QueryBuilder          dump()                                                              Dump the current SQL and bindings.
 * @method static bool                  exists()                                                            Determine if any rows exist for the current query.
 * @method static SupportCollection     explain()                                                           Explains the query.
 * @method static array                 getBindings()                                                       Get the current query value bindings in a flattened array.
 * @method static ConnectionInterface   getConnection()                                                     Get the database connection instance.
 * @method static Grammar               getGrammar()                                                        Get the query grammar instance.
 * @method static bool                  insert(array $values)                                               Insert new records into the database.
 * @method static int                   insertGetId(array $values, ?string $sequence = null)                Insert a new record and get the value of the primary key.
 * @method static int                   insertOrIgnore(array $values)                                       Insert new records into the database while ignoring errors.
 * @method static int                   insertUsing(array $columns, Closure|QueryBuilder|string $query)     Insert new records into the table using a subquery.
 * @method static mixed                 max(string $column)                                                 Retrieve the maximum value of a given column.
 * @method static mixed                 min(string $column)                                                 Retrieve the minimum value of a given column.
 * @method static Expression            raw($value)                                                         Create a raw database expression.
 * @method static mixed                 sum(string $column)                                                 Retrieve the sum of the values of a given column.
 * @method static string                toSql()                                                             Get the SQL representation of the query.
 *
 * @see \Illuminate\Database\Concerns\BuildsQueries;
 *
 * @method staitc bool                              chunk(int $count, callable $callback)                                                           Chunk the results of the query.
 * @method static Collection                        chunkMap(callable $callback, int $count = 1000)                                                 Run a map over each item while chunking.
 * @method static bool                              each(callable $callback, int $count = 1000)                                                     Execute a callback over each item while chunking.
 * @method static bool                              chunkById(int $count, callable $callback, ?string $column = null, ?string $alias = null)        Chunk the results of a query by comparing IDs.
 * @method static bool                              eachById(callable $callback, int $count = 1000, ?string $column = null, ?string $alias = null)  Execute a callback over each item while chunking by ID.
 * @method static LazyCollection                    lazy(int $chunkSize = 1000)                                                                     Query lazily, by chunks of the given size.
 * @method static LazyCollection                    lazyById($chunkSize = 1000, ?string $column = null, ?string $alias = null)                      Query lazily, by chunking the results of a query by comparing IDs.
 * @method static LazyCollection                    lazyByIdDesc($chunkSize = 1000, ?string $column = null, ?string $alias = null)                  Query lazily, by chunking the results of a query by comparing IDs in descending order.
 * @method static EloquentModel|object|static|null  first(array|string $columns = ['*'])                                                            Execute the query and get the first result.
 * @method static static|null                       baseSole(array|string $columns = ['*'])                                                         Execute the query and get the first result if it's the sole matching record.
 * @method static $this                             tap(callback $callback)                                                                         Pass the query to a given callback.
 *
 * @see \Illuminate\Database\Eloquent\Concerns\QueriesRelationships
 *
 * @method static Builder|static    has(Relation|string $relation, string $operator = '>=', int $count = 1, string $boolean = 'and', ?Closure $callback = null)                                         Add a relationship count / exists condition to the query.
 * @method static Builder|static    orHas(string $relation, string $operator = '>=', int $count = 1)                                                                                                    Add a relationship count / exists condition to the query with an "or".
 * @method static Builder|static    doesntHave(string $relation, string $boolean = 'and', ?Closure $callback = null)                                                                                    Add a relationship count / exists condition to the query.
 * @method static Builder|static    orDoesntHave(string $relation)                                                                                                                                      Add a relationship count / exists condition to the query with an "or".
 * @method static Builder|static    whereHas(string $relation, ?Closure $callback = null, string $operator = '>=', int $count = 1)                                                                      Add a relationship count / exists condition to the query with where clauses.
 * @method static Builder|static    withWhereHas(string $relation, ?Closure $callback = null, string $operator = '>=', int $count = 1)                                                                  Add a relationship count / exists condition to the query with where clauses. \n Also load the relationship with same condition.
 * @method static Builder|static    orWhereHas(string $relation, ?Closure $callback = null, string $operator = '>=', int $count = 1)                                                                    Add a relationship count / exists condition to the query with where clauses and an "or".
 * @method static Builder|static    whereDoesntHave(string $relation, ?Closure $callback = null)                                                                                                        Add a relationship count / exists condition to the query with where clauses.
 * @method static Builder|static    orWhereDoesntHave(string $relation, ?Closure $callback = null)                                                                                                      Add a relationship count / exists condition to the query with where clauses and an "or".
 * @method static Builder|static    hasMorph(MorphTo|string $relation, string|array $types, string $operator = '>=', int $count = 1, string $boolean = 'and', ?Closure $callback = null)                Add a relationship count / exists condition to the query with where clauses and an "or".
 * @method static Builder|static    orHasMorph(MorphTo|string $relation, string|array $types, string $operator = '>=', int $count = 1)                                                                  Add a polymorphic relationship count / exists condition to the query with an "or".
 * @method static Builder|static    doesntHaveMorph(MorphTo|string $relation, string|array $types, string $boolean = 'and', ?Closure $callback = null)                                                  Add a polymorphic relationship count / exists condition to the query.
 * @method static Builder|static    orDoesntHaveMorph(MorphTo|string $relation, string|array $types)                                                                                                    Add a polymorphic relationship count / exists condition to the query with an "or".
 * @method static Builder|static    whereHasMorph(MorphTo|string $relation, string|array $types, ?Closure $callback = null, string $operator = '>=', int $count = 1)                                    Add a polymorphic relationship count / exists condition to the query with where clauses.
 * @method static Builder|static    orWhereHasMorph(MorphTo|string $relation, string|array $types, ?Closure $callback = null, string $operator = '>=', int $count = 1)                                  Add a polymorphic relationship count / exists condition to the query with where clauses and an "or".
 * @method static Builder|static    whereDoesntHaveMorph(MorphTo|string $relation, string|array $types, ?Closure $callback = null)                                                                      Add a polymorphic relationship count / exists condition to the query with where clauses.
 * @method static Builder|static    orWhereDoesntHaveMorph(MorphTo|string $relation, string|array $types, ?Closure $callback = null)                                                                    Add a polymorphic relationship count / exists condition to the query with where clauses and an "or".
 * @method static Builder|static    whereRelation(string $relation, Closure|string|array|Expression $column, $operator = null, $value = null)                                                           Add a basic where clause to a relationship query.
 * @method static Builder|static    orWhereRelation(string $relation, Closure|string|array|Expression $column, $operator = null, $value = null)                                                         Add an "or where" clause to a relationship query.
 * @method static Builder|static    whereMorphRelation(MorphTo|string $relation, string|array $types, Closure|string|array|Expression $column, $operator = null, $value = null)                         Add a polymorphic relationship condition to the query with a where clause.
 * @method static Builder|static    orWhereMorphRelation(MorphTo|string $relation, string|array $types, Closure|string|array|Expression $column, $operator = null, $value = null)                       Add a polymorphic relationship condition to the query with an "or where" clause.
 * @method static Builder|static    whereMorphedTo(MorphTo|string $relation, EloquentModel|string $model, $boolean = 'and')                                                                             Add a morph-to relationship condition to the query.
 * @method static Builder|static    whereNotMorphedTo(MorphTo|string $relation, EloquentModel|string $model, $boolean = 'and')                                                                          Add a not morph-to relationship condition to the query.
 * @method static Builder|static    orWhereMorphedTo(MorphTo|string $relation, EloquentModel|string $model)                                                                                             Add a morph-to relationship condition to the query with an "or where" clause.
 * @method static Builder|static    orWhereNotMorphedTo(MorphTo|string $relation, EloquentModel|string $model)                                                                                          Add a not morph-to relationship condition to the query with an "or where" clause.
 * @method static $this             whereBelongsTo(EloquentModel|\Illuminate\Database\Eloquent\Collection<EloquentModel> $related, ?string $relationshipName = null, string $boolean = 'and')           Add a "belongs to" relationship where clause to the query.
 * @method static $this             orWhereBelongsTo(EloquentModel $related, ?string $relationshipName = null)                                                                                          Add an "BelongsTo" relationship with an "or where" clause to the query.
 * @method static $this             withAggregate($relations, string $column, string $function = null)                                                                                                  Add subselect queries to include an aggregate value for a relationship.
 * @method static $this             withCount($relations)                                                                                                                                               Add subselect queries to count the relations.
 * @method static $this             withMax(string|array $relation, string $column)                                                                                                                     Add subselect queries to include the max of the relation's column.
 * @method static $this             withMin(string|array $relation, string $column)                                                                                                                     Add subselect queries to include the min of the relation's column.
 * @method static $this             withSum(string|array $relation, string $column)                                                                                                                     Add subselect queries to include the sum of the relation's column.
 * @method static $this             withAvg(string|array $relation, string $column)                                                                                                                     Add subselect queries to include the average of the relation's column.
 * @method static $this             withExists(string|array $relation)                                                                                                                                  Add subselect queries to include the existence of related models.
 * @method static Builder|static    mergeConstraintsFrom(Builder $from)                                                                                                                                 Merge the where constraints from another query to the current query.
 *
 * @see \Illuminate\Database\Concerns\ExplainsQueries
 *
 * @method SupportCollection    explain()   Explains the query.
 *
 * @see \Illuminate\Database\Query\Builder
 *
 * @method $this                select(array|mixed $columns = ['*'])                                                                                                                                                    Set the columns to be selected.
 * @method $this                selectSub(Closure|QueryBuilder|string $query, string $as)                                                                                                                               Add a subselect expression to the query.
 * @method $this                selectRaw(string $expression, array $bindings = [])                                                                                                                                     Add a new "raw" select expression to the query.
 * @method $this                fromSub(Closure|QueryBuilder|string $query, string $as)                                                                                                                                 Makes "from" fetch from a subquery.
 * @method $this                fromRaw(string $expression, $bindings = [])                                                                                                                                             Add a raw from clause to the query.
 * @method $this                addSelect(array|mixed $column)                                                                                                                                                          Add a new select column to the query.
 * @method $this                distinct()                                                                                                                                                                              Force the query to only return distinct results.
 * @method $this                from(Closure|QueryBuilder|string $table, ?string $as = null)                                                                                                                            Set the table which the query is targeting.
 * @method $this                join(string $table, Closure|string $first, ?string $operator = null, ?string $second = null, string $type = 'inner', bool $where = false)                                               Add a join clause to the query.
 * @method $this                joinWhere(string $table, Closure|string $first, ?string $operator, ?string $second, string $type = 'inner')                                                                             Add a "join where" clause to the query.
 * @method $this                joinSub(Closure|QueryBuilder|Builder|string $query, string $as, Closure|string $first, ?string $operator = null, ?string $second = null, string $type = 'inner', bool $where = false)   Add a subquery join clause to the query.
 * @method $this                leftJoin(string $table, Closure|string $first, ?string $operator = null, ?string $second = null)                                                                                        Add a left join to the query.
 * @method $this                leftJoinWhere(string $table, Closure|string $first, string $operator, string $second)                                                                                                   Add a "join where" clause to the query.
 * @method $this                leftJoinSub(Closure|QueryBuilder|Builder|string $query, string $as, Closure|string $first, ?string $operator = null, ?string $second = null)                                            Add a subquery left join to the query.
 * @method $this                rightJoin(string $table, Closure|string $first, ?string $operator = null, ?string $second = null)                                                                                       Add a right join to the query.
 * @method $this                rightJoinWhere(string $table, Closure|string  $first, string $operator, string $second)                                                                                                 Add a "right join where" clause to the query.
 * @method $this                rightJoinSub(Closure|QueryBuilder|Builder|string $query, string $as, Closure|string $first, ?string $operator = null, ?string $second = null)                                           Add a subquery right join to the query.
 * @method $this                crossJoin(string $table, Closure|string|null $first = null, ?string $operator = null, ?string $second = null)                                                                           Add a "cross join" clause to the query.
 * @method $this                crossJoinSub(Closure|QueryBuilder|string $query, string $as)                                                                                                                            Add a subquery cross join to the query.
 * @method $this                mergeWheres(array $wheres, array $bindings)                                                                                                                                             Merge an array of where clauses and bindings.
 * @method $this                where(Closure|string|array $column, $operator = null, $value = null, string $boolean = 'and')                                                                                           Add a basic where clause to the query.
 * @method $this                prepareValueAndOperator(string $value, string $operator, bool $useDefault = false)                                                                                                      Prepare the value and operator for a where clause.
 * @method $this                orWhere(Closure|string|array $column, $operator = null, $value = null)                                                                                                                  Add an "or where" clause to the query.
 * @method $this                whereNot(Closure|string|array $column, $operator = null, $value = null, string $boolean = 'and')                                                                                        Add a basic "where not" clause to the query.
 * @method $this                orWhereNot(Closure|string|array $column, $operator = null, $value = null)                                                                                                               Add an "or where not" clause to the query.
 * @method $this                whereColumn(string|array $first, ?string $operator = null, ?string $second = null, ?string $boolean = 'and')                                                                            Add a "where" clause comparing two columns to the query.
 * @method $this                orWhereColumn(string|array $first, ?string $operator = null, ?string $second = null)                                                                                                    Add an "or where" clause comparing two columns to the query.
 * @method $this                whereRaw(string $sql, $bindings = [], string $boolean = 'and')                                                                                                                          Add a raw where clause to the query.
 * @method $this                orWhereRaw(string $sql, $bindings = [])                                                                                                                                                 Add a raw or where clause to the query.
 * @method $this                whereIn(string $column, $values, string $boolean = 'and', bool $not = false)                                                                                                            Add a "where in" clause to the query.
 * @method $this                orWhereIn(string $column, $values)                                                                                                                                                      Add an "or where in" clause to the query.
 * @method $this                whereNotIn(string $column, $values, string $boolean = 'and')                                                                                                                            Add a "where not in" clause to the query.
 * @method $this                orWhereNotIn(string $column, $values)                                                                                                                                                   Add an "or where not in" clause to the query.
 * @method $this                whereIntegerInRaw(string $column, Arrayable|array $values, string $boolean = 'and', bool $not = false)                                                                                  Add a "where in raw" clause for integer values to the query.
 * @method $this                orWhereIntegerInRaw(string $column, Arrayable|array $values)                                                                                                                            Add an "or where in raw" clause for integer values to the query.
 * @method $this                whereIntegerNotInRaw(string $column, Arrayable|array $values, string $boolean = 'and')                                                                                                  Add a "where not in raw" clause for integer values to the query.
 * @method $this                orWhereIntegerNotInRaw(string $column, Arrayable|array $values)                                                                                                                         Add an "or where not in raw" clause for integer values to the query.
 * @method $this                whereNull(string|array $columns, string $boolean = 'and', bool $not = false)                                                                                                            Add a "where null" clause to the query.
 * @method $this                orWhereNull(string $column)                                                                                                                                                             Add an "or where null" clause to the query.
 * @method $this                whereNotNull(string|array $columns, string $boolean = 'and')                                                                                                                            Add a "where not null" clause to the query.
 * @method $this                whereBetween(string|Expression $column, array $values, string $boolean = 'and', bool $not = false)                                                                                      Add a where between statement to the query.
 * @method $this                whereBetweenColumns(string $column, array $values, string $boolean = 'and', bool $not = false)                                                                                          Add a where between statement using columns to the query.
 * @method $this                orWhereBetween(string $column, array $values)                                                                                                                                           Add an or where between statement to the query.
 * @method $this                orWhereBetweenColumns(string $column, array $values)                                                                                                                                    Add an or where between statement using columns to the query.
 * @method $this                whereNotBetween(string $column, array $values, string $boolean = 'and')                                                                                                                 Add a where not between statement to the query.
 * @method $this                whereNotBetweenColumns(string $column, array $values, string $boolean = 'and')                                                                                                          Add a where not between statement using columns to the query.
 * @method $this                orWhereNotBetween(string $column, array $values)                                                                                                                                        Add an or where not between statement to the query.
 * @method $this                orWhereNotBetweenColumns(string $column, array $values)                                                                                                                                 Add an or where not between statement using columns to the query.
 * @method $this                orWhereNotNull(string $column)                                                                                                                                                          Add an "or where not null" clause to the query.
 * @method $this                whereDate(string $column, string $operator, DateTimeInterface|string|null $value = null, string $boolean = 'and')                                                                       Add a "where date" statement to the query.
 * @method $this                orWhereDate(string $column, string $operator, DateTimeInterface|string|null $value = null)                                                                                              Add an "or where date" statement to the query.
 * @method $this                whereTime(string $column, string $operator, DateTimeInterface|string|null $value = null, string $boolean = 'and')                                                                       Add a "where time" statement to the query.
 * @method $this                orWhereTime(string $column, string $operator, DateTimeInterface|string|null $value = null)                                                                                              Add an "or where time" statement to the query.
 * @method $this                whereDay(string $column, string $operator, DateTimeInterface|string|null $value = null, string $boolean = 'and')                                                                        Add a "where day" statement to the query.
 * @method $this                orWhereDay(string $column, string $operator, DateTimeInterface|string|null $value = null)                                                                                               Add an "or where day" statement to the query.
 * @method $this                whereMonth(string $column, string $operator, DateTimeInterface|string|null $value = null, string $boolean = 'and')                                                                      Add a "where month" statement to the query.
 * @method $this                orWhereMonth(string $column, string $operator, DateTimeInterface|string|null $value = null)                                                                                             Add an "or where month" statement to the query.
 * @method $this                whereYear(string $column, string $operator, DateTimeInterface|string|null $value = null, string $boolean = 'and')                                                                       Add a "where year" statement to the query.
 * @method $this                orWhereYear(string $column, string $operator, DateTimeInterface|string|null $value = null)                                                                                              Add an "or where year" statement to the query.
 * @method $this                whereNested(Closure $callback, string $boolean = 'and')                                                                                                                                 Add a nested where statement to the query.
 * @method $this                forNestedWhere()                                                                                                                                                                        Create a new query instance for nested where condition.
 * @method $this                addNestedWhereQuery(QueryBuilder $query, string $boolean = 'and')                                                                                                                       Add another query builder as a nested where to the query builder.
 * @method $this                whereSub(string $column, string $operator, Closure $callback, string $boolean)                                                                                                          Add a full sub-select to the query.
 * @method $this                whereExists(Closure $callback, string $boolean = 'and', bool $not = false)                                                                                                              Add an exists clause to the query.
 * @method $this                orWhereExists(Closure $callback, bool $not = false)                                                                                                                                     Add an or exists clause to the query.
 * @method $this                whereNotExists(Closure $callback, string $boolean = 'and')                                                                                                                              Add a where not exists clause to the query.
 * @method $this                orWhereNotExists(Closure $callback)                                                                                                                                                     Add a where not exists clause to the query.
 * @method $this                addWhereExistsQuery(QueryBuilder $query, string $boolean = 'and', bool $not = false)                                                                                                    Add an exists clause to the query.
 * @method $this                whereRowValues(array $columns, string $operator, array $values, string $boolean = 'and')                                                                                                Adds a where condition using row values.
 * @method $this                orWhereRowValues(array $columns, string $operator, array $values)                                                                                                                       Adds an or where condition using row values.
 * @method $this                whereJsonContains(string $column, $value, string $boolean = 'and', bool $not = false)                                                                                                   Add a "where JSON contains" clause to the query.
 * @method $this                orWhereJsonContains(string $column, $value)                                                                                                                                             Add an "or where JSON contains" clause to the query.
 * @method $this                whereJsonDoesntContain(string $column, $value, string $boolean = 'and')                                                                                                                 Add a "where JSON not contains" clause to the query.
 * @method $this                orWhereJsonDoesntContain(string $column, $value)                                                                                                                                        Add an "or where JSON not contains" clause to the query.
 * @method $this                whereJsonLength(string $column, $operator, $value = null, string $boolean = 'and')                                                                                                      Add a "where JSON length" clause to the query.
 * @method $this                orWhereJsonLength(string $column, $operator, $value = null)                                                                                                                             Add an "or where JSON length" clause to the query.
 * @method $this                dynamicWhere(string $method, array $parameters)                                                                                                                                         Handles dynamic "where" clauses to the query.
 * @method $this                groupBy(array|string ...$groups)                                                                                                                                                        Add a "group by" clause to the query.
 * @method $this                groupByRaw(string $sql, array $bindings = [])                                                                                                                                           Add a raw groupBy clause to the query.
 * @method $this                having(string $column, ?string $operator = null, ?string $value = null, string $boolean = 'and')                                                                                        Add a "having" clause to the query.
 * @method $this                orHaving(string $column, ?string $operator = null, ?string $value = null)                                                                                                               Add an "or having" clause to the query.
 * @method $this                havingBetween(string $column, array $values, string $boolean = 'and', bool $not = false)                                                                                                Add a "having between " clause to the query.
 * @method $this                havingRaw(string $sql, array $bindings = [], string $boolean = 'and')                                                                                                                   Add a raw having clause to the query.
 * @method $this                orHavingRaw(string $sql, array $bindings = [])                                                                                                                                          Add a raw or having clause to the query.
 * @method $this                orderBy(Closure|QueryBuilder|Expression|string $column, string $direction = 'asc')                                                                                                      Add an "order by" clause to the query.
 * @method $this                orderByDesc(string $column)                                                                                                                                                             Add a descending "order by" clause to the query.
 * @method $this                latest(Closure|Builder|Expression|string $column = 'created_at')                                                                                                                        Add an "order by" clause for a timestamp to the query.
 * @method $this                oldest(Closure|Builder|Expression|string $column = 'created_at')                                                                                                                        Add an "order by" clause for a timestamp to the query.
 * @method $this                inRandomOrder(string $seed = '')                                                                                                                                                        Put the query's results in random order.
 * @method $this                orderByRaw(string $sql, array $bindings = [])                                                                                                                                           Add a raw "order by" clause to the query.
 * @method $this                skip(int $value)                                                                                                                                                                        Alias to set the "offset" value of the query.
 * @method $this                offset(int $value)                                                                                                                                                                      Set the "offset" value of the query.
 * @method $this                take(int $value)                                                                                                                                                                        Alias to set the "limit" value of the query.
 * @method $this                limit(int $value)                                                                                                                                                                       Set the "limit" value of the query.
 * @method $this                forPage(int $page, int $perPage = 15)                                                                                                                                                   Set the limit and offset for a given page.
 * @method $this                forPageBeforeId(int $perPage = 15, ?int $lastId = 0, string $column = 'id')                                                                                                             Constrain the query to the previous "page" of results before a given ID.
 * @method $this                forPageAfterId(int $perPage = 15, ?int $lastId = 0, string $column = 'id')                                                                                                              Constrain the query to the next "page" of results after a given ID.
 * @method $this                reorder(?string $column = null, string $direction = 'asc')                                                                                                                              Remove all existing orders and optionally add a new order.
 * @method $this                union(QueryBuilder|Closure $query, bool $all = false)                                                                                                                                   Add a union statement to the query.
 * @method $this                unionAll(QueryBuilder|Closure $query)                                                                                                                                                   Add a union all statement to the query.
 * @method $this                lock(string|bool $value = true)                                                                                                                                                         Lock the selected rows in the table.
 * @method $this                lockForUpdate()                                                                                                                                                                         Lock the selected rows in the table for updating.
 * @method $this                sharedLock()                                                                                                                                                                            Share lock the selected rows in the table.
 * @method string               toSql()                                                                                                                                                                                 Get the SQL representation of the query.
 * /method mixed|static         find(int|string $id, array|string $columns = ['*'])                                                                                                                                     Execute a query for a single record by ID.
 * @method mixed|static         findOr(int|string $id, array|string $columns = ['*'], Closure $callback = null)                                                                                                         Execute a query for a single record by ID or call a callback.
 * @method mixed                value(string $column)                                                                                                                                                                   Get a single column's value from the first result of a query.
 * @method mixed                soleValue(string $column)                                                                                                                                                               Get a single column's value from the first result of a query if it's the sole matching record.
 * /method SupportCollection    get(array|string $columns = ['*'])                                                                                                                                                      Execute the query as a "select" statement.
 * @method LengthAwarePaginator paginate(int|Closure $perPage = 15, array|string $columns = ['*'], string $pageName = 'page', ?int $page = null)                                                                        Paginate the given query into a simple paginator.
 * @method Paginator            simplePaginate(int|Closure $perPage = 15, array|string $columns = ['*'], strung $pageName = 'page', ?int $page = null)                                                                  Get a paginator only supporting simple next and previous links. \n This is more efficient on larger data-sets, etc.
 * @method CursorPaginator      cursorPaginate(?int $perPage = 15, array|string $columns = ['*'], string $cursorName = 'cursor', Cursor|string|null $cursor = null)                                                     Get a paginator only supporting simple next and previous links. \n This is more efficient on larger data-sets, etc.
 * @method $this                getCountForPagination(array $columns = ['*'])                                                                                                                                           Get the count of the total records for the paginator.
 * @method LazyCollection       cursor()                                                                                                                                                                                Get a lazy collection for the given query.
 * @method SupportCollection    pluck(string $column, ?string $key = null)                                                                                                                                              Get a collection instance containing the values of a given column.
 * @method $this                implode(string $column, string $glue = '')                                                                                                                                              Concatenate values of a given column as a string.
 * @method bool                 exists()                                                                                                                                                                                Determine if any rows exist for the current query.
 * @method bool                 doesntExist()                                                                                                                                                                           Determine if no rows exist for the current query.
 * @method $this                existsOr(Closure $callback)                                                                                                                                                             Execute the given callback if no rows exist for the current query.
 * @method $this                doesntExistOr(Closure $callback)                                                                                                                                                        Execute the given callback if rows exist for the current query.
 * @method int                  count(string $columns = '*')                                                                                                                                                            Retrieve the "count" result of the query.
 * @method mixed                min(string $column)                                                                                                                                                                     Retrieve the minimum value of a given column.
 * @method mixed                max(string $column)                                                                                                                                                                     Retrieve the maximum value of a given column.
 * @method mixed                sum(string $column)                                                                                                                                                                     Retrieve the sum of the values of a given column.
 * @method mixed                avg(string $column)                                                                                                                                                                     Retrieve the average of the values of a given column.
 * @method mixed                average(string $column)                                                                                                                                                                 Alias for the "avg" method.
 * @method $this                aggregate(string $function, array $columns = ['*'])                                                                                                                                     Execute an aggregate function on the database.
 * @method $this                numericAggregate(string $function, array $columns = ['*'])                                                                                                                              Execute a numeric aggregate function on the database.
 * @method bool                 insert(array $values)                                                                                                                                                                   Insert new records into the database.
 * @method int                  insertOrIgnore(array $values)                                                                                                                                                           Insert new records into the database while ignoring errors.
 * @method int                  insertGetId(array $values, ?string $sequence = null)                                                                                                                                    Insert a new record and get the value of the primary key.
 * @method int                  insertUsing(array $columns, Closure|QueryBuilder|string $query)                                                                                                                         Insert new records into the table using a subquery.
 * @method int                  update(array $values)                                                                                                                                                                   Update records in the database.
 * @method int                  updateFrom(array $values)                                                                                                                                                               Update records in a PostgreSQL database using the update from syntax.
 * @method $this                updateOrInsert(array $attributes, array $values = [])                                                                                                                                   Insert or update a record matching the attributes, and fill it with values.
 * @method int                  upsert(array $values, array|string $uniqueBy, ?array $update = null)                                                                                                                    Insert new records or update the existing ones.
 * @method int                  increment(string $column, float|int $amount = 1, array $extra = [])                                                                                                                     Increment a column's value by a given amount.
 * @method int                  decrement(string $column, float|int $amount = 1, array $extra = [])                                                                                                                     Decrement a column's value by a given amount.
 * @method $this                truncate()                                                                                                                                                                              Run a truncate statement on the table.
 * @method $this                newQuery()                                                                                                                                                                              Get a new instance of the query builder.
 * @method Expression           raw($value)                                                                                                                                                                             Create a raw database expression.
 * @method array                getBindings()                                                                                                                                                                           Get the current query value bindings in a flattened array.
 * @method $this                getRawBindings()                                                                                                                                                                        Get the raw array of bindings.
 * @method $this                setBindings(array $bindings, string $type = 'where')                                                                                                                                    Set the bindings on the query builder.
 * @method $this                addBinding($value, string $type = 'where')                                                                                                                                              Add a binding to the query.
 * @method mixed                castBinding($value)                                                                                                                                                                     Cast the given binding value.
 * @method $this                mergeBindings(QueryBuilder $query)                                                                                                                                                      Merge an array of bindings into our bindings.
 * @method $this                cleanBindings(array $bindings)                                                                                                                                                          Remove all of the expressions from a list of bindings.
 * @method ConnectionInterface  getConnection()                                                                                                                                                                         Get the database connection instance.
 * @method $this                getProcessor()                                                                                                                                                                          Get the database query processor instance.
 * @method Grammar              getGrammar()                                                                                                                                                                            Get the query grammar instance.
 * @method $this                useWritePdo()                                                                                                                                                                           Use the write pdo for query.
 * @method $this                clone()                                                                                                                                                                                 Clone the query.
 * @method $this                cloneWithout(array $properties)                                                                                                                                                         Clone the query without the given properties.
 * @method $this                cloneWithoutBindings(array $except)                                                                                                                                                     Clone the query without the given bindings.
 * @method QueryBuilder         dump()                                                                                                                                                                                  Dump the current SQL and bindings.
 * @method void                 dd()                                                                                                                                                                                    Die and dump the current SQL and bindings.
 *
 * @codingStandardsIgnoreEnd
 */
abstract class Model extends EloquentModel
{
    public $timestamps = false;

    protected $guarded = [];

    /**
     * 获取表名
     */
    public static function getTableName(): string
    {
        $class = static::class;
        return (new $class())->getTable();
    }

    /**
     * 获取表数据
     *
     * @return array
     * [
     *  'datas' => \Illuminate\Database\Eloquent\Collection,
     *  'count' => int
     * ]
     */
    public static function getTableDataFromAdmin(\Slim\Http\Request $request, ?callable $callback = null, ?callable $precondition = null): array
    {
        //得到排序的方式
        $order = $request->getParam('order')[0]['dir'];
        //得到排序字段的下标
        $order_column = $request->getParam('order')[0]['column'];
        //根据排序字段的下标得到排序字段
        $order_field = $request->getParam('columns')[$order_column]['data'];
        if ($callback !== null) {
            call_user_func_array($callback, [&$order_field]);
        }
        $limit_start = $request->getParam('start');
        $limit_length = $request->getParam('length');
        $search = $request->getParam('search')['value'];

        $query = self::query();
        if ($precondition !== null) {
            call_user_func($precondition, $query);
        }
        if ($search) {
            $query->where(
                static function ($query) use ($search): void {
                    $query->where('id', 'LIKE binary', "%${search}%");
                    $attributes = Capsule::schema()->getColumnListing(self::getTableName());
                    foreach ($attributes as $s) {
                        if ($s !== 'id') {
                            $query->orwhere($s, 'LIKE binary', "%${search}%");
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
