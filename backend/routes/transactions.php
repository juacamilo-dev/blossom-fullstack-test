<?php

function generateTraceNumber() {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $trace = '';
    for ($i = 0; $i < 12; $i++) {
        $trace .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return $trace;
}

switch ($method) {
    case 'GET':
        $query = "SELECT * FROM transactions WHERE 1=1";
        $params = [];

        if (!empty($_GET['type'])) {
            $query .= " AND type = :type";
            $params[':type'] = $_GET['type'];
        }

        if (!empty($_GET['date_from'])) {
            $query .= " AND creationDate >= :date_from";
            $params[':date_from'] = $_GET['date_from'];
        }

        if (!empty($_GET['date_to'])) {
            $query .= " AND creationDate <= :date_to";
            $params[':date_to'] = $_GET['date_to'];
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;

        $countQuery = str_replace("SELECT *", "SELECT COUNT(*)", $query);
        $countStmt = $pdo->prepare($countQuery);
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();

        $query .= " ORDER BY creationDate DESC LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $transactions = $stmt->fetchAll();

        http_response_code(200);
        echo json_encode([
            "data" => $transactions,
            "pagination" => [
                "total" => $total,
                "page" => $page,
                "limit" => $limit,
                "total_pages" => ceil($total / $limit)
            ]
        ]);
        break;

    case 'POST':
        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body['accountNumberFrom']) ||
            empty($body['accountNumberTypeFrom']) ||
            empty($body['accountNumberTo']) ||
            empty($body['accountNumberTypeTo']) ||
            empty($body['amount']) ||
            empty($body['type'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing required fields"]);
            exit();
        }

        if (!in_array($body['type'], ['credit', 'debit'])) {
            http_response_code(400);
            echo json_encode(["error" => "Type must be credit or debit"]);
            exit();
        }

        if (!is_numeric($body['amount']) || $body['amount'] <= 0) {
            http_response_code(400);
            echo json_encode(["error" => "Amount must be a positive number"]);
            exit();
        }

        do {
            $traceNumber = generateTraceNumber();
            $check = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE traceNumber = ?");
            $check->execute([$traceNumber]);
        } while ($check->fetchColumn() > 0);

        $stmt = $pdo->prepare("
            INSERT INTO transactions 
            (accountNumberFrom, accountNumberTypeFrom, accountNumberTo, 
             accountNumberTypeTo, traceNumber, amount, type, description, reference)
            VALUES 
            (:accountNumberFrom, :accountNumberTypeFrom, :accountNumberTo,
             :accountNumberTypeTo, :traceNumber, :amount, :type, :description, :reference)
        ");

        $stmt->execute([
            ':accountNumberFrom' => $body['accountNumberFrom'],
            ':accountNumberTypeFrom' => $body['accountNumberTypeFrom'],
            ':accountNumberTo' => $body['accountNumberTo'],
            ':accountNumberTypeTo' => $body['accountNumberTypeTo'],
            ':traceNumber' => $traceNumber,
            ':amount' => $body['amount'],
            ':type' => $body['type'],
            ':description' => $body['description'] ?? null,
            ':reference' => $body['reference'] ?? null
        ]);

        $newId = $pdo->lastInsertId();
        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE transactionID = ?");
        $stmt->execute([$newId]);
        $transaction = $stmt->fetch();

        http_response_code(201);
        echo json_encode([
            "message" => "Transaction created successfully",
            "data" => $transaction
        ]);
        break;

    case 'DELETE':
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "Transaction ID is required"]);
            exit();
        }

        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE transactionID = ?");
        $stmt->execute([$id]);
        $transaction = $stmt->fetch();

        if (!$transaction) {
            http_response_code(404);
            echo json_encode(["error" => "Transaction not found"]);
            exit();
        }

        $stmt = $pdo->prepare("DELETE FROM transactions WHERE transactionID = ?");
        $stmt->execute([$id]);

        http_response_code(200);
        echo json_encode([
            "message" => "Transaction deleted successfully",
            "data" => $transaction
        ]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}