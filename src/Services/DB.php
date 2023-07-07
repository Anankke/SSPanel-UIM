<?php

declare(strict_types=1);

namespace App\Services;

use Closure;
use Exception;
use Generator;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseTransactionsManager;
use Illuminate\Database\Grammar;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\Grammars\Grammar as QueryGrammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Database\Schema\Builder as SchemaBuilder;
use PDO;
use PDOStatement;

/**
 * @codingStandardsIgnoreStart
 *
 * @see Connection
 *
 * @method static void              useDefaultQueryGrammar()                                                Set the query grammar to the default implementation.
 * @method static void              useDefaultSchemaGrammar()                                               Set the schema grammar to the default implementation.
 * @method static void              useDefaultPostProcessor()                                               Set the query post processor to the default implementation.
 * @method static SchemaBuilder     getSchemaBuilder()                                                      Get a schema builder instance for the connection.
 * @method static Builder           table(Closure|Builder|string $table, string|null $as = null)            Begin a fluent query against a database table.
 * @method static Builder           query()                                                                 Get a new query builder instance.
 * @method static mixed             selectOne(string $query, array $bindings = [], bool $useReadPdo = true) Run a select statement and return a single result.
 * @method static array             selectFromWriteConnection(string $query, array $bindings = [])          Run a select statement against the database.
 * @method static array             select(string $query, array $bindings = [], bool $useReadPdo = true)    Run a select statement against the database.
 * @method static Generator        cursor(string $query, array $bindings = [], bool $useReadPdo = true)    Run a select statement against the database and returns a generator.
 * @method static bool              insert(string $query, array $bindings = [])                             Run an insert statement against the database.
 * @method static int               update(string $query, array $bindings = [])                             Run an update statement against the database.
 * @method static int               delete(string $query, array $bindings = [])                             Run a delete statement against the database.
 * @method static bool              statement(string $query, array $bindings = [])                          Execute an SQL statement and return the boolean result.
 * @method static int               affectingStatement(string $query, array $bindings = [])                 Run an SQL statement and get the number of rows affected.
 * @method static bool              unprepared($query)                                                      Run a raw, unprepared query against the PDO connection.
 * @method static array             pretend(Closure $callback)                                              Execute the given callback in "dry run" mode.
 * @method static void              bindValues(PDOStatement $statement, array $bindings)                   Bind values to their parameters in the given statement.
 * @method static array             prepareBindings(array $bindings)                                        Prepare the query bindings for execution.
 * @method static void              logQuery(string $query, array $bindings, float|null $time = null)       Log a query in the connection's query log.
 * @method static void              reconnect()                                                             Reconnect to the database.
 * @method static void              disconnect()                                                            Disconnect from the underlying PDO connection.
 * @method static void              listen(Closure $callback)                                               Register a database query listener with the connection.
 * @method static Expression        raw($value)                                                             Get a new raw query expression.
 * @method static void              recordsHaveBeenModified(bool $value = true)                             Indicate if any records have been modified.
 * @method static void              forgetRecordModificationState()                                         Reset the record modification state.
 * ---- Method about Doctrine which is not available should be here. -----
 * @method static PDO               getPdo()                                                                Get the current PDO connection.
 * @method static PDO|Closure|null  getRawPdo()                                                             Get the current PDO connection parameter without executing any reconnect logic.
 * @method static PDO               getReadPdo()                                                            Get the current PDO connection used for reading.
 * @method static PDO|Closure|null  getRawReadPdo()                                                         Get the current read PDO connection parameter without executing any reconnect logic.
 * @method static Connection        setPdo(PDO|Closure|null $pdo)                                           Set the PDO connection.
 * @method static Connection        setReconnector(callable $reconnector)                                   Set the reconnect instance on the connection.
 * @method static string|null       getName()                                                               Get the database connection name.
 * @method static mixed             getConfig(string|null $option = null)                                   Get an option from the configuration options.
 * @method static string            getDriverName()                                                         Get the PDO driver name.
 * @method static QueryGrammar      getQueryGrammar()                                                       Get the query grammar used by the connection.
 * @method static Connection        setQueryGrammar(QueryGrammar $grammar)                                  Set the query grammar used by the connection.
 * @method static QueryGrammar      getSchemaGrammar()                                                      Get the schema grammar used by the connection.
 * @method static Connection        setSchemaGrammar(QueryGrammar $grammar)                                 Set the schema grammar used by the connection.
 * @method static Processor         getPostProcessor()                                                      Get the query post processor used by the connection.
 * @method static Connection        setPostProcessor(Processor $processor)                                  Set the query post processor used by the connection.
 * @method static Dispatcher        getEventDispatcher()                                                    Get the event dispatcher used by the connection.
 * @method static Connection        setEventDispatcher(Dispatcher $events)                                  Set the event dispatcher instance on the connection.
 * @method static void              unsetEventDispatcher()                                                  Unset the event dispatcher for this connection.
 * @method static Connection        setTransactionManager(DatabaseTransactionsManager $manager)             Set the transaction manager instance on the connection.
 * @method static void              unsetTransactionManager()                                               Unset the transaction manager for this connection.
 * @method static bool              pretending()                                                            Determine if the connection is in a "dry run".
 * @method static array             getQueryLog()                                                           Get the connection query log.
 * @method static void              flushQueryLog()                                                         Clear the query log.
 * @method static void              enableQueryLog()                                                        Enable the query log on the connection.
 * @method static void              disableQueryLog()                                                       Disable the query log on the connection.
 * @method static bool              logging()                                                               Determine whether we're logging queries.
 * @method static string            getDatabaseName()                                                       Get the name of the connected database.
 * @method static Connection        setDatabaseName(string $database)                                       Set the name of the connected database.
 * @method static string            getTablePrefix()                                                        Get the table prefix for the connection.
 * @method static Connection        setTablePrefix(string $prefix)                                          Set the table prefix in use by the connection.
 * @method static Grammar           withTablePrefix(Grammar $grammar)                                       Set the table prefix and return the grammar.
 * @method static void              resolverFor(string $driver, Closure $callback)                          Register a connection resolver.
 * @method static mixed             getResolver(string $driver) Get the connection                          resolver for the given driver.
 *
 * @see \Illuminate\Database\Concerns\ManagesTransactions
 *
 * @method static mixed             transaction(Closure $callback, int $attempts = 1)                       Execute a Closure within a transaction.
 * @method static void              beginTransaction()                                                      Start a new database transaction.
 * @method static void              commit()                                                                Commit the active database transaction.
 * @method static void              rollBack(int|null $toLevel = null)                                      Rollback the active database transaction.
 * @method static int               transactionLevel()                                                      Get the number of active transactions.
 * @method static void              afterCommit(callable $callback)                                         Execute the callback after a transaction commits.
 *
 * @codingStandardsIgnoreEnd
 */
final class DB extends Manager
{
    public static function init(): void
    {
        $db = new DB();

        try {
            $db->addConnection(Config::getDbConfig());
            $db->getConnection()->getPdo();
        } catch (Exception $e) {
            die('Could not connect to main database: ' . $e->getMessage());
        }

        $db->setAsGlobal();
        $db->bootEloquent();

        View::$connection = $db->getDatabaseManager();
        $db->getDatabaseManager()->connection('default')->enableQueryLog();
    }
}
