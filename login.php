<?php
session_start();

// تنظیمات اتصال به دیتابیس
$host = 'localhost';
$dbname = 'music';
$username = 'music';
$password = ''; // لطفاً در محیط واقعی از رمز عبور قوی‌تری استفاده کنید و آن را امن نگه دارید.

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            phone_number VARCHAR(15) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
} catch (PDOException $e) {
    // در محیط توسعه می‌توانید پیام دقیق خطا را نمایش دهید، اما در محیط عملیاتی پیام عمومی‌تری بهتر است.
    error_log("Database Connection Error: " . $e->getMessage()); // لاگ کردن خطا برای بررسی‌های بعدی
    die(json_encode(['error' => 'خطا در اتصال به پایگاه داده. لطفاً بعداً تلاش کنید.']));
}

$error = null;
$successMessage = null; // برای نمایش پیام موفقیت احتمالی

// بررسی درخواست فرم (ثبت‌نام یا ورود)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // بهتر است از filter_input برای دسترسی به متغیرهای POST استفاده شود.
    $phoneNumberInput = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_STRING);
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING) ?? 'login';

    // اعتبارسنجی شماره موبایل (مثال برای ایران)
    if (!preg_match('/^09[0-9]{9}$/', $phoneNumberInput)) {
        $error = "شماره موبایل وارد شده نامعتبر است.";
    } else {
        if ($action === 'register') {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE phone_number = ?");
            $stmt->execute([$phoneNumberInput]);
            $user = $stmt->fetch();

            if ($user) {
                $error = "این شماره موبایل قبلاً ثبت شده است. لطفاً وارد شوید.";
            } else {
                try {
                    $stmt = $pdo->prepare("INSERT INTO users (phone_number) VALUES (?)");
                    $stmt->execute([$phoneNumberInput]);

                    $_SESSION['user_id'] = $pdo->lastInsertId();
                    $_SESSION['phone_number'] = $phoneNumberInput;

                    // به جای هدایت مستقیم، می‌توانید یک پیام موفقیت تنظیم کنید
                    // و اجازه دهید کاربر به صورت دستی به صفحه اصلی برود یا پس از چند ثانیه هدایت شود.
                    // $successMessage = "ثبت نام با موفقیت انجام شد! در حال هدایت به صفحه اصلی...";
                    header("Location: index.php"); // یا هر صفحه دیگری که می‌خواهید کاربر به آن هدایت شود
                    exit();
                } catch (PDOException $e) {
                    error_log("Registration Error: " . $e->getMessage());
                    $error = "خطایی در هنگام ثبت نام رخ داد. لطفاً دوباره تلاش کنید.";
                }
            }
        } elseif ($action === 'login') {
            $stmt = $pdo->prepare("SELECT id, phone_number FROM users WHERE phone_number = ?");
            $stmt->execute([$phoneNumberInput]);
            $user = $stmt->fetch();

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['phone_number'] = $user['phone_number'];
                header("Location: index.php"); // یا هر صفحه دیگری
                exit();
            } else {
                $error = "شماره موبایل یافت نشد. لطفاً ابتدا ثبت نام کنید.";
            }
        }
    }
}

// بررسی درخواست خروج
if (isset($_GET['logout'])) {
    session_unset(); // حذف همه متغیرهای سشن
    session_destroy(); // نابود کردن سشن
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?')); // هدایت به همین صفحه بدون پارامترهای query string
    exit();
}

$page_mode = isset($_GET['register']) ? 'register' : 'login';
$page_title = ($page_mode === 'register' ? 'ثبت نام در Fomico' : 'ورود به Fomico');
?>
<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <style>
        :root {
            --primary-bg-color: #1a1a2e; /* پس زمینه اصلی تیره */
            --secondary-bg-color: #16213e; /* پس زمینه فرم */
            --accent-color: #0f3460; /* رنگ تاکید (مثلا برای دکمه ها) */
            --text-color: #e0e0e0; /* رنگ متن اصلی */
            --input-bg-color: #2c3e50; /* پس زمینه اینپوت */
            --input-border-color: #537ec5; /* رنگ بوردر اینپوت */
            --link-color: #537ec5; /* رنگ لینک */
            --error-color: #e74c3c; /* رنگ خطا */
            --success-color: #2ecc71; /* رنگ موفقیت */
            --logo-filter: invert(90%) sepia(15%) saturate(500%) hue-rotate(180deg) brightness(100%) contrast(90%); /* فیلتر برای روشن کردن لوگوی تیره روی پس زمینه تیره */
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            background-color: var(--primary-bg-color);
            color: var(--text-color);
            display: flex;
            flex-direction: column; /* برای قرار دادن لوگو و پیام‌ها بالای فرم */
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }

        .logo-container {
            margin-bottom: 20px;
            text-align: center;
        }

        .logo-container img {
            max-width: 150px; /* یا اندازه دلخواه شما */
            height: auto;
            /* اگر لوگوی شما تیره است و روی پس‌زمینه تیره خوب دیده نمی‌شود، این فیلتر می‌تواند کمک کند آن را روشن‌تر نشان دهد */
            /* filter: var(--logo-filter); */
            /* اگر لوگوی شما از قبل برای تم تاریک مناسب است، فیلتر بالا را کامنت کنید یا حذف کنید */
        }
        
        .brand-name {
            font-size: 2.5em;
            font-weight: bold;
            color: var(--link-color); /* یا هر رنگ دیگری که برای برندتان می‌پسندید */
            margin-bottom: 20px;
        }

        .form-container {
            background: var(--secondary-bg-color);
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .form-container h2 {
            color: var(--text-color);
            margin-bottom: 25px;
            font-weight: 500;
        }

        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 0.9em;
            text-align: center;
        }

        .error {
            background-color: rgba(231, 76, 60, 0.1); /* Light red background */
            color: var(--error-color);
            border: 1px solid var(--error-color);
        }
        
        .success {
            background-color: rgba(46, 204, 113, 0.1); /* Light green background */
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group input[type="tel"],
        .input-group input[type="text"] /* Fallback if tel is not used */
        {
            width: 100%;
            padding: 12px 15px;
            background-color: var(--input-bg-color);
            color: var(--text-color);
            border: 1px solid var(--input-border-color);
            border-radius: 6px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            text-align: right; /* برای شماره موبایل فارسی */
        }

        .input-group input[type="tel"]::placeholder,
        .input-group input[type="text"]::placeholder {
            color: #8899a6;
        }

        .input-group input[type="tel"]:focus,
        .input-group input[type="text"]:focus {
            border-color: var(--link-color);
            box-shadow: 0 0 0 3px rgba(83, 126, 197, 0.3);
            outline: none;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .submit-btn:hover {
            background-color: #1e4f8a; /* کمی روشن‌تر یا تیره‌تر از accent-color */
        }

        .switch-form {
            margin-top: 25px;
            font-size: 0.9em;
        }

        .switch-form p {
            margin: 0;
            color: #b0bec5; /* رنگ ملایم‌تر برای متن */
        }

        .switch-form a {
            color: var(--link-color);
            text-decoration: none;
            font-weight: bold;
        }

        .switch-form a:hover {
            text-decoration: underline;
        }
        
        .logout-link {
            margin-top: 30px;
            text-align: center;
        }
        .logout-link a {
            color: var(--link-color);
            text-decoration: none;
            font-size: 0.9em;
        }
        .logout-link a:hover {
            text-decoration: underline;
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .form-container {
                padding: 20px 25px;
            }
            .brand-name {
                font-size: 2em;
            }
            .form-container h2 {
                font-size: 1.3em;
            }
        }
    </style>
</head>
<body>
    <div class="logo-container">
        <img src="logo.png" alt="لوگوی Fomico">
        <div class="brand-name">Fomico</div>
    </div>

    <?php if ($error): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($successMessage): ?>
        <div class="message success"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="form-container">
            <h2>خوش آمدید، <?php echo htmlspecialchars($_SESSION['phone_number']); ?>!</h2>
            <p>شما با موفقیت وارد سیستم شده‌اید.</p>
            <p><a href="index.html" class="submit-btn" style="display: inline-block; text-decoration: none; margin-top: 15px; text-align:center;">رفتن به صفحه اصلی</a></p>
            <div class="logout-link">
                <a href="?logout=1">خروج از حساب کاربری</a>
            </div>
        </div>
    <?php else: ?>
        <div class="form-container">
            <h2><?php echo ($page_mode === 'register' ? 'ایجاد حساب کاربری' : 'ورود به حساب کاربری'); ?></h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . ($page_mode === 'register' ? '?register=1' : ''); ?>">
                <input type="hidden" name="action" value="<?php echo $page_mode; ?>">
                <div class="input-group">
                    <input type="tel" name="phone_number" placeholder="شماره موبایل (مثال: 09123456789)" required pattern="^09[0-9]{9}$" title="شماره موبایل باید با 09 شروع شود و 11 رقم باشد.">
                </div>
                <button type="submit" class="submit-btn">
                    <?php echo ($page_mode === 'register' ? 'ثبت نام' : 'ورود'); ?>
                </button>
            </form>

            <div class="switch-form">
                <?php if ($page_mode === 'login'): ?>
                    <p>حساب کاربری ندارید؟ <a href="?register=1">یکی بسازید!</a></p>
                <?php else: ?>
                    <p>قبلاً ثبت نام کرده‌اید؟ <a href="<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>">وارد شوید!</a></p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</body>
</html>