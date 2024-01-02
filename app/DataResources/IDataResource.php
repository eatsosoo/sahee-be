<?php

namespace App\DataResources;

interface IDataResource
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array;

    /**
     * dynamical register extra
     *
     * @param string $fieldName
     * @return void
     */
    public function withField(string $fieldName): void;

    /**
     * dynamical register extra fields
     *
     * @param array<string> $fields
     * @return void
     */
    public function withFields(array $fields): void;

    /**
     * return the model class of this resource
     */
    public function modelClass(): string;
}
