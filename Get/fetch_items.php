<?php


$host = 'localhost';
$db   = 'inventory';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB Connection failed: '.$e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// helper to read JSON body
function getJsonBody() {
    $body = file_get_contents('php://input');
    return json_decode($body, true) ?? [];
}

try {
    if ($method === 'GET') {
        // Optional search q param
        $q = $_GET['q'] ?? '';
        if ($q !== '') {
            $stmt = $pdo->prepare("SELECT id, item_name, rate, tax_rate, description FROM items
                                   WHERE item_name LIKE :q ORDER BY item_name ASC");
            $stmt->execute([':q' => "%$q%"]);
        } else {
            $stmt = $pdo->query("SELECT id, item_name, rate, tax_rate, description FROM items ORDER BY item_name ASC");
        }
        $items = $stmt->fetchAll();
        echo json_encode($items);
        exit;
    }

    if ($method === 'POST') {
        $data = $_POST;
        // if JSON POST, fallback
        if (empty($data)) $data = getJsonBody();

        if (empty($data['item_name'])) {
            http_response_code(422);
            echo json_encode(['error' => 'item_name is required']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO items (item_name, rate, tax_rate, description) VALUES (:name, :rate, :tax, :desc)");
        $stmt->execute([
            ':name' => $data['item_name'],
            ':rate' => $data['rate'] ?? 0,
            ':tax'  => $data['tax_rate'] ?? 0,
            ':desc' => $data['description'] ?? null,
        ]);

        $id = $pdo->lastInsertId();
        $stmt = $pdo->prepare("SELECT id, item_name, rate, tax_rate, description FROM items WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch();

        echo json_encode($item);
        exit;
    }

    if ($method === 'PUT') {
        // Expect JSON body
        $data = getJsonBody();
        if (empty($data['id'])) {
            http_response_code(422);
            echo json_encode(['error' => 'id is required']);
            exit;
        }
        $stmt = $pdo->prepare("UPDATE items SET item_name = :name, rate = :rate, tax_rate = :tax, description = :desc WHERE id = :id");
        $stmt->execute([
            ':name' => $data['item_name'] ?? '',
            ':rate' => $data['rate'] ?? 0,
            ':tax'  => $data['tax_rate'] ?? 0,
            ':desc' => $data['description'] ?? null,
            ':id'   => $data['id'],
        ]);

        $stmt = $pdo->prepare("SELECT id, item_name, rate, tax_rate, description FROM items WHERE id = ?");
        $stmt->execute([$data['id']]);
        echo json_encode($stmt->fetch());
        exit;
    }

    if ($method === 'DELETE') {
        // Expect id param in query or JSON body
        parse_str(file_get_contents('php://input'), $deleteParams); // fallback for form-encoded
        $data = getJsonBody();
        $id = $_GET['id'] ?? $deleteParams['id'] ?? $data['id'] ?? null;

        if (!$id) {
            http_response_code(422);
            echo json_encode(['error' => 'id is required']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['deleted' => (int)$id]);
        exit;
    }

    // Method not allowed
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
} catch (PDOException $ex) {
    http_response_code(500);
    echo json_encode(['error' => $ex->getMessage()]);
}
