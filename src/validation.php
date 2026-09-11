<?php
/**
 * Form input cleaning and validation.
 */

/**
 * Read one text field from the submitted form, safely.
 * - Missing field  -> empty string (no "undefined index" error)
 * - Not a string   -> empty string (someone sent name[]=... to break the code)
 * - Spaces at the start and end are removed
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

/**
 * Allowed values for status and priority.
 * The keys match the ENUM values in the database, the values are shown to the user.
 */
const ISSUE_STATUSES = [
    'open'        => 'Open',
    'in_progress' => 'In progress',
    'done'        => 'Done',
];

const ISSUE_PRIORITIES = [
    'low'    => 'Low',
    'medium' => 'Medium',
    'high'   => 'High',
];

/**
 * Check an issue. Returns errors as ['field' => 'message'].
 */
function validate_issue(array $issue): array
{
    $errors = [];

    if ($issue['title'] === '') {
        $errors['title'] = 'Title is required.';
    } elseif (mb_strlen($issue['title']) < 3) {
        $errors['title'] = 'Title must be at least 3 characters.';
    } elseif (mb_strlen($issue['title']) > 150) {
        $errors['title'] = 'Title can be at most 150 characters.';
    }

    if (mb_strlen($issue['description']) > 2000) {
        $errors['description'] = 'Description can be at most 2000 characters.';
    }

    if (!array_key_exists($issue['status'], ISSUE_STATUSES)) {
        $errors['status'] = 'Choose a valid status.';
    }

    if (!array_key_exists($issue['priority'], ISSUE_PRIORITIES)) {
        $errors['priority'] = 'Choose a valid priority.';
    }

    return $errors;
}