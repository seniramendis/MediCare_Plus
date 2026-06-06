<?php
session_start();
require_once 'db_connect.php';

/**
 * Check whether the current user is authenticated.
 *
 * @return bool
 */
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

/**
 * Get the current authenticated user details.
 *
 * @return array|null
 */
function current_user()
{
    if (!is_logged_in()) {
        return null;
    }

    return fetch_user_by_id((int) $_SESSION['user_id']);
}

/**
 * Get the current authenticated user role.
 *
 * @return string
 */
function current_user_role()
{
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'guest';
}

/**
 * Redirect guests to the login page.
 */
function require_login()
{
    if (!is_logged_in()) {
        http_response_code(302);
        header('Location: Login.php');
        exit(0);
    }
}

/**
 * Require a specific role or set of roles to access a page.
 *
 * @param string|array $roles
 */
function require_role($roles)
{
    require_login();
    if (is_string($roles)) {
        $roles = [$roles];
    }

    if (!in_array(current_user_role(), $roles, true)) {
        http_response_code(302);
        header('Location: Home.php');
        exit(0);
    }
}

/**
 * Simple HTML encoding helper.
 *
 * @param string $value
 * @return string
 */
function e($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
