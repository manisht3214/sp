
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function getDefaultUsers() {
    return [
        [
            'id' => 1,
            'username' => 'admin',
            'password' => 'admin123',
            'user_type' => 'admin',
            'name' => 'Administrator'
        ],
        [
            'id' => 2,
            'username' => 'user',
            'password' => 'user123',
            'user_type' => 'user',
            'name' => 'Standard User'
        ]
    ];
}

function getDatabaseUsers() {
    if (!isset($GLOBALS['pdo'])) {
        return [];
    }

    try {
        $GLOBALS['pdo']->query("SELECT 1 FROM users LIMIT 1");
        $stmt = $GLOBALS['pdo']->query("SELECT * FROM users ORDER BY id");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function ensureDefaultUsersTable() {
    if (!isset($GLOBALS['pdo'])) {
        return false;
    }

    try {
        $GLOBALS['pdo']->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                user_type ENUM('admin','user') NOT NULL DEFAULT 'user',
                full_name VARCHAR(150) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $seed = [
            ['admin', 'admin123', 'admin', 'Administrator'],
            ['user', 'user123', 'user', 'Standard User']
        ];

        foreach ($seed as $row) {
            $stmt = $GLOBALS['pdo']->prepare(
                "INSERT IGNORE INTO users (username, password, user_type, full_name) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute($row);
        }

        return true;
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return false;
    }
}

function getAllUsers() {
    if (isset($GLOBALS['pdo'])) {
        ensureDefaultUsersTable();
        $databaseUsers = getDatabaseUsers();
        if (!empty($databaseUsers)) {
            $formatted = [];
            foreach ($databaseUsers as $user) {
                $formatted[] = [
                    'id' => (int)($user['id'] ?? 0),
                    'username' => (string)($user['username'] ?? ''),
                    'password' => (string)($user['password'] ?? ''),
                    'user_type' => (string)($user['user_type'] ?? 'user'),
                    'name' => (string)($user['full_name'] ?? ucfirst(($user['user_type'] ?? 'user')))
                ];
            }
            return $formatted;
        }
    }

    $users = getDefaultUsers();
    $customUsers = $_SESSION['custom_users'] ?? [];

    if (!empty($customUsers)) {
        $users = array_merge($users, $customUsers);
    }

    return $users;
}

function currentUser() {
    return $_SESSION['user'] ?? null;
}

function isLoggedIn() {
    return !empty($_SESSION['user']);
}

function isAdmin() {
    return isLoggedIn() && ($_SESSION['user']['user_type'] ?? '') === 'admin';
}

function loginUser($username, $password) {
    $username = strtolower(trim((string)$username));

    if (isset($GLOBALS['pdo'])) {
        try {
            $stmt = $GLOBALS['pdo']->prepare(
                "SELECT * FROM users WHERE username = ? AND password = ? LIMIT 1"
            );
            $stmt->execute([$username, (string)$password]);
            $user = $stmt->fetch();

            if ($user) {
                $_SESSION['user'] = [
                    'id' => (int)($user['id'] ?? 0),
                    'username' => (string)($user['username'] ?? ''),
                    'name' => (string)($user['full_name'] ?? ucfirst($userType)),
                    'user_type' => (string)($user['user_type'] ?? 'user')
                ];
                return true;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    foreach (getAllUsers() as $user) {
        if (
            strtolower((string)($user['username'] ?? '')) === $username
            && (string)($user['password'] ?? '') === (string)$password           
        ) {
            $_SESSION['user'] = [
                'id' => (int)($user['id'] ?? 0),
                'username' => (string)($user['username'] ?? ''),
                'name' => (string)($user['name'] ?? ucfirst($user['user_type'] ?? 'user')),
                'user_type' => (string)($user['user_type'] ?? 'user')
            ];
            return true;
        }
    }

    return false;
}

function registerUser($username, $password, $userType, $name = '') {
    $username = strtolower(trim((string)$username));
    $password = trim((string)$password);
    $userType = in_array($userType, ['admin', 'user'], true) ? $userType : 'user';

    if ($username === '' || $password === '') {
        return false;
    }

    if (isset($GLOBALS['pdo'])) {
        try {
            ensureDefaultUsersTable();
            $stmt = $GLOBALS['pdo']->prepare(
                "SELECT id FROM users WHERE username = ?"
            );
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                return false;
            }

            $insert = $GLOBALS['pdo']->prepare(
                "INSERT INTO users (username, password, user_type, full_name) VALUES (?, ?, ?, ?)"
            );
            $insert->execute([
                $username,
                $password,
                $userType,
                $name !== '' ? $name : ucfirst($userType)
            ]);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    foreach (getAllUsers() as $user) {
        if (strtolower((string)($user['username'] ?? '')) === $username) {
            return false;
        }
    }

    $customUsers = $_SESSION['custom_users'] ?? [];
    $customUsers[] = [
        'id' => time() + count($customUsers) + 1,
        'username' => $username,
        'password' => $password,
        'user_type' => $userType,
        'name' => $name !== '' ? $name : ucfirst($userType)
    ];

    $_SESSION['custom_users'] = $customUsers;
    return true;
}

function logoutUser() {
    unset($_SESSION['user']);
}

function requireLogin($redirect = 'login.php') {
    if (!isLoggedIn()) {
        header('Location: ' . $redirect);
        exit;
    }
}

function requireAdmin($redirect = 'login.php') {
    requireLogin($redirect);

    if (!isAdmin()) {
        header('Location: ' . $redirect . '?message=unauthorized');
        exit;
    }
}

function getSports($pdo) {
    return $pdo->query(
        "SELECT * FROM sports WHERE status = 1 ORDER BY name"
    )->fetchAll();
}

function getSport($pdo, $slug) {
    $stmt = $pdo->prepare(
        "SELECT * FROM sports WHERE slug = ? AND status = 1"
    );
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

function getCategories($pdo, $sportId) {
    $stmt = $pdo->prepare("
        SELECT c.*, sc.sport_id
        FROM categories c
        JOIN sport_categories sc ON sc.category_id = c.id
        WHERE sc.sport_id = ?
        ORDER BY c.id
    ");
    $stmt->execute([$sportId]);
    return $stmt->fetchAll();
}

function getCategory($pdo, $sportId, $slug) {
    $stmt = $pdo->prepare("
        SELECT c.*
        FROM categories c
        JOIN sport_categories sc ON sc.category_id = c.id
        WHERE sc.sport_id = ? AND c.slug = ?
    ");
    $stmt->execute([$sportId, $slug]);
    return $stmt->fetch();
}

function profilePhoto($photo) {
    return !empty($photo)
        ? 'assets/uploads/' . rawurlencode(basename($photo))
        : 'assets/uploads/default-avatar.png';
}

function countCategory($pdo, $sportId, $categoryId) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM person_sports
        WHERE sport_id = ? AND category_id = ?
    ");
    $stmt->execute([$sportId, $categoryId]);
    return (int)$stmt->fetchColumn();
}
