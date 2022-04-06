<?php

namespace phpcommon\Utils\QueryBuilder;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator as BasePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use phpcommon\Handler\Exceptions\BAD_QUERY_STRING_EXCEPTION;

class QueryBuilder
{
    protected Model $model;

    protected UriParser $uriParser;

    protected array $wheres = [];

    protected mixed $orderBy = [];

    protected mixed $limit;

    protected int $page = 1;

    protected int $offset = 0;

    protected array $columns;

    protected array $relationColumns = [];

    protected array $includes = [];

    protected Builder $query;

    protected mixed $result;

    public function __construct(Model $model, Request $request)
    {
        $this->orderBy = ['id' => 'asc'];

        $this->limit = 15;

        $this->columns = $model->getVisible();

        $this->model = $model;

        $this->uriParser = new UriParser($request);

        $this->query = $this->model->newQuery();
    }

    public function build(): static
    {
        $this->prepare();

        if ($this->hasWheres()) {
            array_map([$this, 'addWhereToQuery'], $this->wheres);
        }

        if ($this->hasLimit()) {
            $this->query->take($this->limit);
        }

        if ($this->hasOffset()) {
            $this->query->skip($this->offset);
        }

        $this->addOrderByToQuery($this->orderBy);

        $this->query->select($this->columns);

        $this->query->with($this->includes);


        return $this;
    }

    protected function prepare(): static
    {
        $this->setWheres($this->uriParser->whereParameters());

        $constantParameters = $this->uriParser->constantParameters();

        array_map([$this, 'prepareConstant'], $constantParameters);

        if ($this->hasIncludes() && $this->hasRelationColumns()) {
            $this->fixRelationColumns();
        }

        return $this;
    }

    private function setWheres($parameters)
    {
        $this->wheres = $parameters;
    }

    private function hasIncludes(): bool
    {
        return (count($this->includes) > 0);
    }

    private function hasRelationColumns(): bool
    {
        return (count($this->relationColumns) > 0);
    }

    private function fixRelationColumns()
    {
        $keys = array_keys($this->relationColumns);

        $callback = [$this, 'fixRelationColumn'];

        array_map($callback, $keys, $this->relationColumns);
    }

    private function hasWheres(): bool
    {
        return (count($this->wheres) > 0);
    }

    private function hasLimit()
    {
        return ($this->limit);
    }

    private function hasOffset(): bool
    {
        return ($this->offset != 0);
    }

    private function addOrderByToQuery($orders): void
    {
        foreach ($orders as $order => $direction)
            $this->query->orderBy($order, $direction);
    }

    public function get(): Collection|array
    {
        return $this->query->get();
    }

    /**
     * @throws Exception
     */
    public function paginate(): Paginator
    {
        if (!$this->hasLimit()) {
            throw new BAD_QUERY_STRING_EXCEPTION('You cannot use unlimited option for pagination');
        }

        return $this->basePaginate($this->limit);
    }

    private function basePaginate(int $perPage = null): Paginator
    {
        $page = BasePaginator::resolveCurrentPage();

        $perPage = $perPage ?: $this->model->getPerPage();

        if (method_exists($this->query, 'toBase')) {
            $query = $this->query->toBase();
        } else {
            $query = $this->query->getQuery();
        }

        $total = $query->getCountForPagination();

        $results = $total ? $this->query->forPage($page, $perPage)->get($this->columns) : new Collection;

        return (new Paginator($results, $total, $perPage, $page, [
            'path' => BasePaginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]))->setQueryUri($this->uriParser->getQueryUri());
    }

    private function prepareConstant($parameter)
    {
        if (!$this->uriParser->hasQueryParameter($parameter)) {
            return;
        }

        $callback = [$this, $this->setterMethodName($parameter)];

        $callbackParameter = $this->uriParser->queryParameter($parameter);

        call_user_func($callback, $callbackParameter['value']);
    }

    private function setterMethodName($key): string
    {
        return 'set' . Str::studly($key);
    }

    private function setIncludes($includes)
    {
        $this->includes = array_filter(explode(',', $includes));
    }

    private function setPage($page)
    {
        $this->page = (int)$page;

        $this->offset = ($page - 1) * $this->limit;
    }

    private function setColumns($columns)
    {
        $columns = array_filter(explode(',', $columns));

        $this->columns = $this->relationColumns = [];

        array_map([$this, 'setColumn'], $columns);
    }

    /**
     * @throws Exception
     */
    private function setColumn($column)
    {
        if ($this->isRelationColumn($column)) {
            $this->appendRelationColumn($column);
            return;
        }

        if (!$this->hasTableColumn($column) || !$this->isColumnVisible($column)) {
            throw new BAD_QUERY_STRING_EXCEPTION("Unknown '$column' column. Make sure you enter the name correctly.");
        }

        $this->columns[] = $column;
    }

    private function isRelationColumn($column): bool
    {
        return (count(explode('.', $column)) > 1);
    }

    private function appendRelationColumn($keyAndColumn)
    {
        list($key, $column) = explode('.', $keyAndColumn);

        $this->relationColumns[$key][] = $column;
    }

    private function hasTableColumn($column): bool
    {
        return (Schema::hasColumn($this->model->getTable(), $column));
    }

    private function isColumnVisible($column): bool
    {
        return (in_array($column, $this->model->getVisible()));
    }

    private function fixRelationColumn($key, $columns)
    {
        $index = array_search($key, $this->includes);

        unset($this->includes[$index]);

        $this->includes[$key] = $this->closureRelationColumns($columns);
    }

    private function closureRelationColumns($columns): Closure
    {
        return function ($q) use ($columns) {
            $q->select($columns);
        };
    }

    private function setOrderBy($order)
    {
        preg_match_all("/(([a-zA-Z-_]+):([a-zA-Z-_]+))/", $order, $matches);

        $this->orderBy = array_combine($matches[2], $matches[3]);
    }

    private function setLimit($limit)
    {
        $limit = ($limit == 'unlimited') ? null : (int)$limit;

        $this->limit = $limit;
    }

    /**
     * @throws Exception
     */
    public function addWhereToQuery($where)
    {
        if (isset($where['values'])) {
            $where['value'] = $where['values'];
        }
        if (!isset($where['operator'])) {
            $where['operator'] = '';
        }

        if (!$this->hasTableColumn($where['key']) || !$this->isColumnVisible($where['key'])) {
            throw new BAD_QUERY_STRING_EXCEPTION("Unknown '" . $where["key"] . "' column. Make sure you enter the name correctly.");
        }

        if (empty($where['value'])) {
            throw new BAD_QUERY_STRING_EXCEPTION($where["key"] . " column value cannot be empty. Use [null] option.");
        }

        if ($where['type'] == 'In') {
            $this->query->whereIn($where['key'], $where['value']);
        } else if ($where['type'] == 'NotIn') {
            $this->query->whereNotIn($where['key'], $where['value']);
        } else {
            if ($where['value'] == '[null]') {
                if ($where['operator'] == '=') {
                    $this->query->whereNull($where['key']);
                } else {
                    $this->query->whereNotNull($where['key']);
                }
            } else {
                $where['value'] = str_replace('=', '', $where['value']);
                $this->query->where($where['key'], $where['operator'], is_numeric($where['value']) ? (float)$where['value'] : $where['value']);
            }
        }

        return $this;
    }

    public function whereInRelation(string $relation, $where) {
        $this->query = $this->query->whereHas($relation, function ($subquery) use ($where) {
            $subquery->where($where);
        });

        return $this;
    }
}
