<?php

namespace Jordy\Http;

interface ResponseListInterface
{
    public function getPrototype(): ResponseInterface;

    public function getItems(): array;

    public function count(): int;

    public function first(): ResponseInterface;

    public function last(): ResponseInterface;

    public function column(string $column): array;

    public function map(string $keyColumn, string $valueColumn): array;

    public function find(string $column, $value);

    public function toNestedArray(): array;
}
