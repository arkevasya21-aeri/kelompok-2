<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pastikan response JSON
    header('Content-Type: application/json; charset=utf-8');

    $action = $_GET['action'] ?? '';

    if ($action === 'login') {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Bandingkan langsung password plaintext sesuai permintaan (TIDAK DI-HASH)
        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Ambil data tambahan berdasarkan role
            if ($user['role'] === 'guru') {
                $stmt = $pdo->prepare("SELECT * FROM guru WHERE user_id = ?");
                $stmt->execute([$user['id']]);
                $guru = $stmt->fetch(PDO::FETCH_ASSOC);
                $_SESSION['nama'] = $guru['nama'] ?? '';
                $_SESSION['nip'] = $guru['NIP'] ?? '';
            } else {
                $stmt = $pdo->prepare("SELECT * FROM siswa WHERE user_id = ?");
                $stmt->execute([$user['id']]);
                $siswa = $stmt->fetch(PDO::FETCH_ASSOC);
                $_SESSION['nama'] = $siswa['nama'] ?? '';
                $_SESSION['nis'] = $siswa['NIS'] ?? '';
            }

            echo json_encode(['status' => 'success', 'message' => 'Login berhasil!']);
            exit();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Username atau password salah!']);
            exit();
        }
    } elseif ($action === 'register') {
        $role = $_POST['role'] ?? '';
        $nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = $_POST['password'] ?? '';

        // Check if username exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            echo json_encode(['status' => 'error', 'message' => 'Username sudah digunakan!']);
            exit();
        }

        try {
            $pdo->beginTransaction();

            // Insert to users table (menyimpan plaintext karena diminta)
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$username, $password, $role]);
            $user_id = $pdo->lastInsertId();

            // Insert to role-specific table
            if ($role === 'guru') {
                $nip = $_POST['nip'] ?? '';
                $stmt = $pdo->prepare("INSERT INTO guru (user_id, nama, NIP) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $nama, $nip]);
            } else {
                $nis = $_POST['nis'] ?? '';
                $stmt = $pdo->prepare("INSERT INTO siswa (user_id, nama, NIS) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $nama, $nis]);
            }

            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Registrasi berhasil!']);
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Registrasi gagal: ' . $e->getMessage()]);
            exit();
        }
    }
}
