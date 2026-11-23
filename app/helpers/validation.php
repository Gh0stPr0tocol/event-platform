<?php

function validateRequired(string $value, string $label, array &$errors): void
{
    if (trim($value) === '') {
        $errors[$label] = ucfirst($label) . ' is required.';
    }
}

function validateEmail(string $email, array &$errors): void
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    }
}

function validatePhone(string $phone, array &$errors): void
{
    $phone = preg_replace('/[\s()-]/', '', trim($phone));
    if (!preg_match('/^(?:07\d{8}|01\d{8}|2547\d{8}|2541\d{8}|\+2547\d{8}|\+2541\d{8})$/', $phone)) {
        $errors['phone'] = 'Enter a valid Kenyan phone number, for example 0712345678 or +254712345678.';
    }
}

function validatePassword(string $password, array &$errors): void
{
    if (strlen($password) < 8
        || !preg_match('/[a-z]/', $password)
        || !preg_match('/[A-Z]/', $password)
        || !preg_match('/\d/', $password)) {
        $errors['password'] = 'Password must be at least 8 characters and include uppercase, lowercase and a number.';
    }
}

function validateRegistration(array $input, callable $emailExists): array
{
    $errors = [];
    validateRequired($input['name'] ?? '', 'name', $errors);
    validateRequired($input['email'] ?? '', 'email', $errors);
    validateEmail(trim($input['email'] ?? ''), $errors);
    validateRequired($input['phone'] ?? '', 'phone', $errors);
    validatePhone(trim($input['phone'] ?? ''), $errors);
    validateRequired($input['region_id'] ?? '', 'region', $errors);
    validatePassword((string) ($input['password'] ?? ''), $errors);

    if (($input['password'] ?? '') !== ($input['password_confirmation'] ?? '')) {
        $errors['password_confirmation'] = 'Passwords do not match.';
    }
    if (empty($errors['email']) && $emailExists(strtolower(trim($input['email'] ?? '')))) {
        $errors['email'] = 'An account with this email already exists.';
    }

    return $errors;
}
