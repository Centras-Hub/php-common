<?php

namespace phpcommon\Utils\QueryBuilder;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class UriParser
{
    const PATTERN = '/!=|=|<=|<|>=|>/';

    const ARRAY_QUERY_PATTERN = '/(.*)\[\]/';

    protected Request $request;

    protected array $constantParameters = [
        'order_by',
        'limit',
        'page',
        'columns',
        'includes',
    ];

    protected string $uri;

    protected string|null $queryUri;

    protected array $queryParameters = [];

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->uri = $request->getRequestUri();

        $this->setQueryUri($this->uri);

        if ($this->hasQueryUri()) {
            $this->setQueryParameters($this->queryUri);
        }
    }

    public static function getPattern(): string
    {
        return self::PATTERN;
    }

    public function queryParameter($key)
    {
        $keys = Arr::pluck($this->queryParameters, 'key');

        $queryParameters = array_combine($keys, $this->queryParameters);

        return $queryParameters[$key];
    }

    public function constantParameters(): array
    {
        return $this->constantParameters;
    }

    public function whereParameters(): array
    {
        return array_filter(
            $this->queryParameters,
            function ($queryParameter) {
                $key = $queryParameter['key'];
                return (!in_array($key, $this->constantParameters));
            }
        );
    }

    private function setQueryUri($uri)
    {
        $explode = explode('?', $uri);

        $this->queryUri = (isset($explode[1])) ? rawurldecode($explode[1]) : null;
    }

    private function setQueryParameters($queryUri)
    {
        $queryParameters = array_filter(explode('&', $queryUri));

        array_map([$this, 'appendQueryParameter'], $queryParameters);
    }

    private function appendQueryParameter($parameter)
    {
        preg_match(self::ARRAY_QUERY_PATTERN, $parameter, $arrayMatches);
        if (count($arrayMatches) > 0) {
            $this->appendQueryParameterAsWhereIn($parameter, $arrayMatches[1]);
            return;
        }

        $this->appendQueryParameterAsBasicWhere($parameter);
    }

    private function appendQueryParameterAsBasicWhere($parameter)
    {
        preg_match(self::PATTERN, $parameter, $matches);

        $operator = $matches[0];

        list($key, $value) = explode($operator, $parameter);


        if (!$this->isConstantParameter($key) && $this->isLikeQuery($value)) {
            $operator = 'like';
            $value = str_replace('*', '%', $value);
        }

        if (!empty($value)){
            $this->queryParameters[] = [
                'type' => 'Basic',
                'key' => $key,
                'operator' => $operator,
                'value' => $value
            ];
        }

    }

    private function appendQueryParameterAsWhereIn($parameter, $key)
    {
        if (str_contains($parameter, '!=')) {
            $type = 'NotIn';
            $separator = '!=';
        } else {
            $type = 'In';
            $separator = '=';
        }

        $index = null;
        foreach ($this->queryParameters as $_index => $queryParameter) {
            if ($queryParameter['type'] == $type && $queryParameter['key'] == $key) {
                $index = $_index;
                break;
            }
        }

        if ($index !== null) {
            $this->queryParameters[$index]['values'][] = explode($separator, $parameter)[1];
        } else {
            $this->queryParameters[] = [
                'type' => $type,
                'key' => $key,
                'values' => [explode($separator, $parameter)[1]]
            ];
        }
    }

    public function hasQueryUri(): bool
    {
        return (bool)$this->queryUri;
    }

    public function getQueryUri(): string
    {
        return $this->queryUri;
    }

    public function hasQueryParameters(): bool
    {
        return count($this->queryParameters) > 0;
    }

    public function hasQueryParameter($key): bool
    {
        $keys = Arr::pluck($this->queryParameters, 'key');

        return (in_array($key, $keys));
    }

    private function isLikeQuery($query): bool|int
    {
        $pattern = "/^\*|\*$/";

        return (preg_match($pattern, $query, $matches));
    }

    private function isConstantParameter($key): bool
    {
        return (in_array($key, $this->constantParameters));
    }
}
