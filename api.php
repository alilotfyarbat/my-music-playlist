<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// اتصال به دیتابیس
require_once 'config.php';

// بررسی نوع درخواست
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'get_songs':
        getSongs();
        break;
    case 'add_song':
        addSong();
        break;
    case 'update_song':
        updateSong();
        break;
    case 'delete_song':
        deleteSong();
        break;
    default:
        echo json_encode(['error' => 'عملیات نامعتبر']);
        break;
}

// دریافت لیست آهنگ‌ها
function getSongs() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM Songs ORDER BY id DESC");
        $stmt->execute();
        $songs = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'songs' => $songs]);
    } catch(PDOException $e) {
        echo json_encode(['error' => 'خطا در دریافت آهنگ‌ها: ' . $e->getMessage()]);
    }
}

// افزودن آهنگ جدید
function addSong() {
    global $pdo;
    
    // دریافت داده‌های ارسالی
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $artist = isset($_POST['artist']) ? trim($_POST['artist']) : '';
    $image_url = isset($_POST['image_url']) ? trim($_POST['image_url']) : '';
    $audio_url = isset($_POST['audio_url']) ? trim($_POST['audio_url']) : '';
    $album = isset($_POST['album']) ? trim($_POST['album']) : '';
    $genre = isset($_POST['genre']) ? trim($_POST['genre']) : '';
    
    // اعتبارسنجی داده‌ها
    if (empty($title) || empty($artist) || empty($audio_url)) {
        echo json_encode(['error' => 'فیلدهای عنوان، خواننده و آدرس فایل صوتی الزامی هستند']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO Songs (title, artist, image_url, audio_url, album, genre) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $artist, $image_url, $audio_url, $album, $genre]);
        
        $newId = $pdo->lastInsertId();
        
        // دریافت اطلاعات آهنگ اضافه شده
        $stmt = $pdo->prepare("SELECT * FROM Songs WHERE id = ?");
        $stmt->execute([$newId]);
        $song = $stmt->fetch();
        
        echo json_encode(['success' => true, 'message' => 'آهنگ با موفقیت اضافه شد', 'song' => $song]);
    } catch(PDOException $e) {
        echo json_encode(['error' => 'خطا در افزودن آهنگ: ' . $e->getMessage()]);
    }
}

// ویرایش آهنگ
function updateSong() {
    global $pdo;
    
    // دریافت داده‌های ارسالی
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $artist = isset($_POST['artist']) ? trim($_POST['artist']) : '';
    $image_url = isset($_POST['image_url']) ? trim($_POST['image_url']) : '';
    $audio_url = isset($_POST['audio_url']) ? trim($_POST['audio_url']) : '';
    $album = isset($_POST['album']) ? trim($_POST['album']) : '';
    $genre = isset($_POST['genre']) ? trim($_POST['genre']) : '';
    
    // اعتبارسنجی داده‌ها
    if ($id <= 0 || empty($title) || empty($artist) || empty($audio_url)) {
        echo json_encode(['error' => 'اطلاعات ناقص است']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE Songs SET title = ?, artist = ?, image_url = ?, audio_url = ?, album = ?, genre = ? WHERE id = ?");
        $stmt->execute([$title, $artist, $image_url, $audio_url, $album, $genre, $id]);
        
        if ($stmt->rowCount() > 0) {
            // دریافت اطلاعات آهنگ به‌روزرسانی شده
            $stmt = $pdo->prepare("SELECT * FROM Songs WHERE id = ?");
            $stmt->execute([$id]);
            $song = $stmt->fetch();
            
            echo json_encode(['success' => true, 'message' => 'آهنگ با موفقیت به‌روزرسانی شد', 'song' => $song]);
        } else {
            echo json_encode(['error' => 'آهنگ مورد نظر یافت نشد']);
        }
    } catch(PDOException $e) {
        echo json_encode(['error' => 'خطا در به‌روزرسانی آهنگ: ' . $e->getMessage()]);
    }
}

// حذف آهنگ
function deleteSong() {
    global $pdo;
    
    // دریافت شناسه آهنگ
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    if ($id <= 0) {
        echo json_encode(['error' => 'شناسه آهنگ نامعتبر است']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM Songs WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'آهنگ با موفقیت حذف شد', 'id' => $id]);
        } else {
            echo json_encode(['error' => 'آهنگ مورد نظر یافت نشد']);
        }
    } catch(PDOException $e) {
        echo json_encode(['error' => 'خطا در حذف آهنگ: ' . $e->getMessage()]);
    }
}