<?php

namespace phpcommon\http;

use Symfony\Component\Finder\Exception\AccessDeniedException;
use Throwable;

abstract class BaseService
{
    private string $model;

    public function __construct(string $model)
    {
        $this->model = $model;
    }

    public function all(int $accessLevel)
    {
        if ($accessLevel < 0 || $accessLevel > max(array_keys($this->model::accessLevels['get'])))
            return $this->model::all(['*']);
        return $this->model::all($this->model::accessLevels['get'][$accessLevel]);
    }

    /**
     * @throws Throwable
     */
    public function create(array $input, int $accessLevel)
    {
        if ($accessLevel < 0 || $accessLevel > max(array_keys($this->model::accessLevels['create'])) || $this->model::accessLevels['create'] === ['*'])
            return $this->model::create($input);
        foreach (array_keys($input) as $key)
            throw_unless(in_array($key, $this->model::accessLevels['create'][$accessLevel]), new AccessDeniedException);
        return $this->model::create($input);
    }

    public function find(array $data, int $accessLevel)
    {
        if ($accessLevel < 0 || $accessLevel > max(array_keys($this->model::accessLevels['get'])))
            return $this->model::where($data)->first();
        
        return $this->model::where($data)->first($this->model::accessLevels['get'][$accessLevel]);
    }

    public function findAll(array $data, int $accessLevel)
    {
        if ($accessLevel < 0 || $accessLevel > max(array_keys($this->model::accessLevels['get'])))
            return $this->model::where($data)->get();
        return $this->model::where($data)->get($this->model::accessLevels['get'][$accessLevel]);
    }

    /**
     * @throws Throwable
     */
    public function update($column, $value, array $input, int $accessLevel)
    {
        if ($accessLevel < 0 || $accessLevel > max(array_keys($this->model::accessLevels['update'])) || $this->model::accessLevels['update'] === ['*'])
            return $this->model::where($column, $value)->update($input);
        foreach (array_keys($input) as $key)
            throw_unless(in_array($key, $this->model::accessLevels['update'][$accessLevel]), new AccessDeniedException);
        return $this->model::where($column, $value)->update($input);
    }

    /**
     * @throws Throwable
     */
    public function destroy($data, int $accessLevel)
    {
        if ($accessLevel < 0 || $accessLevel > max(array_keys($this->model::accessLevels['delete'])) || $this->model::accessLevels['delete'] === ['*'])
            return $this->model::where($data)->delete();
        foreach (array_keys($data) as $key)
            throw_unless(in_array($key, $this->model::accessLevels['delete'][$accessLevel]), new AccessDeniedException);
        return $this->model::where($data)->delete();
    }

    public function view($model, int $accessLevel)
    {
        $data = $model->toArray();
        if ($accessLevel < 0 || $accessLevel > max(array_keys($this->model::accessLevels['get'])))
            return $data;

        $result = array();
        array_walk($data, function($val, $key) use($accessLevel, &$result) {
            if (in_array($key, $this->model::accessLevels['get'][$accessLevel]))
                $result[$key] = $val;
        });

        return $result;
    }
}
