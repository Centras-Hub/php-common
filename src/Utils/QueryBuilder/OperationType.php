<?php

namespace phpcommon\Utils\QueryBuilder;

abstract class OperationType
{
    const CREATE = 'create';
    const GET = 'get';
    const UPDATE = 'update';
    const DELETE = 'delete';
}
