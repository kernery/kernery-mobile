<?php

namespace Kernery\Main\Contracts;

interface MainModel
{
    public static function bootHasUuidsOrIntegerIds(): void;

    public function newUniqueId(): ?string;

    public function getKeyType(): string;

    public function getKey();

    public function getTable();

    public static function determineIfUsingUuidsForId(): bool;

    public static function getTypeOfId(): string;
}
