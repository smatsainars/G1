<?php
/**
 * Form input cleaning and validation.
 */

function clean_string(array $data, string $key): string
{
    $value = $data[$key] ?? '';
    return is_string($value) ? trim($value) : '';
}

/**
 * Check a project. Returns errors as ['field' => 'message'].
 * An empty array means everything is valid.
 */
function validate_project(array $project): array
{
    $errors = [];

    if ($project['name'] === '') {
        $errors['name'] = 'Name is required.';
    } elseif (mb_strlen($project['name']) < 3) {
        $errors['name'] = 'Name must be at least 3 characters.';
    } elseif (mb_strlen($project['name']) > 100) {
        $errors['name'] = 'Name can be at most 100 characters.';
    }

    if (mb_strlen($project['description']) > 1000) {
        $errors['description'] = 'Description can be at most 1000 characters.';
    }

    return $errors;
}