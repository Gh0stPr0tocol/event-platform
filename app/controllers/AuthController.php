<?php

class AuthController
{
    public function __construct(
        private User $users,
        private Region $regions
    ) {}

    public function register(): void
    {
        $errors = [];
        $old = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfErrors = [];
            if (!verifyCsrfToken()) {
                $csrfErrors['csrf'] = 'Your session expired. Please try again.';
            }

            $old = [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'region_id' => trim($_POST['region_id'] ?? ''),
                'password' => (string) ($_POST['password'] ?? ''),
                'password_confirmation' => (string) ($_POST['password_confirmation'] ?? ''),
            ];

            $errors = validateRegistration(
                $old,
                fn(string $email): bool => $this->users->emailExists($email)
            );
            $regionId = filter_var(
                $old['region_id'],
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );
            if ($regionId === false || !$this->regions->find($regionId)) {
                $errors['region_id'] = 'Choose a valid county.';
            }
            $errors = array_merge($errors, $csrfErrors);
            setOldInput([
                'name' => $old['name'],
                'email' => $old['email'],
                'phone' => $old['phone'],
                'region_id' => $old['region_id'],
            ]);

            if (!$errors) {
                try {
                    $this->users->create([
                        'name' => $old['name'],
                        'email' => strtolower($old['email']),
                        'phone' => $old['phone'],
                        'password_hash' => password_hash($old['password'], PASSWORD_DEFAULT),
                        'region_id' => $old['region_id'],
                    ]);
                    setFlash('success', 'Account created. You can now log in.');
                    clearOldInput();
                    redirect('?page=auth&action=login');
                } catch (Throwable $exception) {
                    error_log($exception->getMessage());
                    $errors['general'] = 'We could not create your account. Please try again.';
                }
            }
        }

        $regions = $this->regions->all();
        $old = $_SESSION['old_input'] ?? $old;
        clearOldInput();
        require APP_ROOT . '/app/views/auth/register.php';
    }

    public function login(): void
    {
        $errors = [];
        $old = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken()) {
                $errors['csrf'] = 'Your session expired. Please try again.';
            }

            $old['email'] = trim($_POST['email'] ?? '');
            $password = (string) ($_POST['password'] ?? '');
            validateRequired($old['email'], 'email', $errors);
            validateEmail($old['email'], $errors);
            validateRequired($password, 'password', $errors);

            if (!$errors) {
                $user = $this->users->findByEmail(strtolower($old['email']));
                if (!$user || !password_verify($password, $user['password_hash'])) {
                    $errors['general'] = 'The email or password is incorrect.';
                } else {
                    session_regenerate_id(true);
                    $_SESSION['user'] = [
                        'id' => (int) $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                        'region_id' => $user['region_id'],
                    ];
                    setFlash('success', 'Welcome back, ' . $user['name'] . '!');
                    redirect($user['role'] === 'admin'
                        ? '?page=admin&action=dashboard'
                        : '?page=home&action=index');
                }
            }
            setOldInput(['email' => $old['email']]);
        }

        $old = $_SESSION['old_input'] ?? $old;
        clearOldInput();
        require APP_ROOT . '/app/views/auth/login.php';
    }

    public function logout(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken()) {
            setFlash('error', 'Your session expired. Please try again.');
            redirect('?page=auth&action=login');
        }

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        session_start();
        setFlash('success', 'You have been logged out.');
        redirect('?page=home&action=index');
    }
}