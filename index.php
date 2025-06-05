<?php
session_start();

// اگر کاربر وارد نشده باشد، به صفحه لاگین هدایت شود
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پلی لیست فومیکو</title>
    <!-- Favicon -->
    <link rel="icon" href="logo.png" type="image/png">
    <link rel="shortcut icon" href="logo.png" type="image/png">
    <!-- Bootstrap CSS RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2d3436;
            --secondary-color: #636e72;
            --dark-color: #1e272e;
            --light-color: #dfe6e9;
            --text-color: #f5f6fa;
            --accent-color: #4834d4;
        }
        
        body {
            background-color: var(--dark-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
            direction: rtl;
            text-align: right;
        }
        
        .music-player {
            background: linear-gradient(to bottom right, var(--primary-color), var(--secondary-color));
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            color: var(--text-color);
            padding: 20px;
            margin-top: 20px;
        }
        
        .song-list {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            padding: 15px;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .song-item {
            background-color: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .song-item:hover {
            background-color: rgba(0, 0, 0, 0.4);
            transform: translateY(-2px);
        }
        
        .song-item.active {
            background-color: rgba(0, 0, 0, 0.5);
            border-right: 4px solid var(--accent-color);
            border-left: none;
        }
        
        .song-cover {
            width: 50px;
            height: 50px;
            border-radius: 5px;
            object-fit: cover;
            margin-left: 15px;
            margin-right: 0;
        }
        
        .song-info {
            flex-grow: 1;
        }
        
        .song-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .song-artist {
            font-size: 0.8rem;
            opacity: 0.8;
        }
        
        .song-actions {
            display: flex;
            gap: 10px;
        }
        
        .song-actions button {
            background: none;
            border: none;
            color: var(--text-color);
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.2s ease;
        }
        
        .song-actions button:hover {
            color: var(--accent-color);
        }
        
        .player-controls {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }
        
        .player-controls:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }
        
        .current-song {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .album-art-container {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            margin-left: 15px;
            margin-right: 0;
        }
        
        .album-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0) 50%);
            z-index: 1;
        }
        
        .current-song-cover {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            object-fit: cover;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }
        
        .album-art-container:hover .current-song-cover {
            transform: scale(1.05);
        }
        
        .progress-container {
            height: 6px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            margin: 10px 0;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: height 0.2s ease;
        }
        
        .progress-container:hover {
            height: 8px;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--accent-color), #6c5ce7);
            border-radius: 5px;
            width: 0%;
            position: relative;
            box-shadow: 0 0 10px rgba(72, 52, 212, 0.5);
        }
        
        .progress-bar::after {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 10px;
            height: 10px;
            background-color: #fff;
            border-radius: 50%;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        
        .progress-container:hover .progress-bar::after {
            opacity: 1;
        }
        
        .time-info {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            opacity: 0.8;
        }
        
        .control-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-top: 15px;
        }
        
        .control-buttons button {
            background: none;
            border: none;
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .control-btn.play-btn {
            background-color: var(--accent-color);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            box-shadow: 0 4px 10px rgba(72, 52, 212, 0.5);
        }
        
        .control-buttons .play-pause {
            font-size: 2.5rem;
        }
        
        .control-buttons .prev-next {
            font-size: 1.5rem;
        }
        
        .control-buttons button:hover {
            transform: scale(1.1);
            color: var(--text-color);
        }
        
        .control-btn.play-btn:hover {
            background-color: #5649c0;
            box-shadow: 0 6px 15px rgba(72, 52, 212, 0.6);
        }
        
        .volume-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
            padding: 5px 10px;
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        
        .volume-container:hover {
            background-color: rgba(0, 0, 0, 0.3);
        }
        
        .volume-container i {
            color: var(--text-color);
            font-size: 0.9rem;
            transition: color 0.2s ease;
        }
        
        .volume-container:hover i {
            color: var(--accent-color);
        }
        
        .volume-slider {
            flex-grow: 1;
            height: 4px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            cursor: pointer;
            position: relative;
            transition: height 0.2s ease;
        }
        
        .volume-slider:hover {
            height: 6px;
        }
        
        .volume-progress {
            height: 100%;
            background: linear-gradient(90deg, var(--accent-color), #6c5ce7);
            border-radius: 5px;
            width: 50%;
            position: relative;
        }
        
        .add-song-form {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .form-control {
            background-color: rgba(0, 0, 0, 0.3);
            border: none;
            color: var(--text-color);
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .form-control:focus {
            background-color: rgba(0, 0, 0, 0.4);
            box-shadow: none;
            color: var(--text-color);
        }
        
        .btn-add {
            background-color: var(--accent-color);
            color: var(--text-color);
            border: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .btn-add:hover {
            background-color: #5649c0;
            color: var(--text-color);
            transform: translateY(-2px);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        /* Search box */
        .search-container {
            margin-bottom: 15px;
        }
        
        .search-container .form-control {
            border-radius: 20px;
            padding-right: 40px;
        }
        
        .search-icon {
            position: absolute;
            right: 15px;
            top: 10px;
            color: rgba(255, 255, 255, 0.5);
        }
        
        /* Edit form */
        .edit-form {
            display: none;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 12px;
            margin-top: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 100%;
        }
        
        .edit-form.active {
            display: block;
            animation: fadeIn 0.3s ease;
            border: 1px solid var(--accent-color);
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.8));
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .edit-form .form-control {
            margin-bottom: 12px;
            background-color: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-color);
            padding: 12px 15px;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-size: 14px;
        }
        
        .edit-form .form-control:focus {
            background-color: rgba(0, 0, 0, 0.4);
            border-color: var(--accent-color);
            box-shadow: 0 0 0 2px rgba(72, 52, 212, 0.25);
        }
        
        @media (max-width: 768px) {
            .edit-form .form-control {
                font-size: 16px;
                padding: 14px 15px;
            }
        }
        
        .edit-form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 10px;
        }

        .edit-form-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .edit-form-close {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.2s ease;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            width: 30px;
            height: 30px;
        }

        .edit-form-close:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .edit-form-body {
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
        }
        
        .edit-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .btn-save, .btn-cancel {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s ease;
            border: none;
            flex: 1;
        }
        
        .btn-save {
            background-color: var(--accent-color);
            color: white;
        }
        
        .btn-save:hover {
            background-color: #5649c0;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(72, 52, 212, 0.3);
        }
        
        .btn-cancel {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .btn-cancel:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        @media (max-width: 768px) {
            .edit-buttons {
                flex-direction: row;
                gap: 15px;
            }
            
            .btn-save, .btn-cancel {
                padding: 12px 20px;
                font-size: 16px;
            }
        }
        
        /* Edit form positioning */
        .edit-form {
            position: relative;
            margin-top: 10px;
            margin-bottom: 10px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        
        @media (max-width: 768px) {
            .edit-form {
                width: 100%;
                margin-top: 15px;
                margin-bottom: 15px;
                padding: 20px;
                border-radius: 12px;
                background-color: rgba(0, 0, 0, 0.6);
                border: 1px solid var(--accent-color);
            }
            
            .edit-form.active {
                display: block;
            }

            
            .edit-form .form-control {
                font-size: 16px; /* Better for mobile touch */
                padding: 12px 15px;
            }
            
            .edit-buttons {
                flex-direction: column;
                gap: 12px;
            }
            
            .btn-save, .btn-cancel {
                padding: 12px 20px;
                font-size: 16px;
            }
        }
            
            .music-player {
                padding: 15px;
                margin-top: 10px;
            }
            
            .current-song-cover {
                width: 60px;
                height: 60px;
            }
            
            .control-buttons .play-pause {
                font-size: 2rem;
            }
            
            .control-buttons .prev-next {
                font-size: 1.2rem;
            }
            @media (max-width: 768px) {
                .music-player {
                    padding: 15px;
                    margin-top: 10px;
                }
                
                .player-controls {
                    padding: 15px;
                }
                
                .current-song-cover {
                    width: 60px;
                    height: 60px;
                }
                
                .control-btn.play-btn {
                    width: 45px;
                    height: 45px;
                }
                
                .control-buttons .play-pause {
                    font-size: 1.8rem;
                }
                
                .control-buttons .prev-next {
                    font-size: 1.2rem;
                }
                
                .control-buttons {
                    gap: 15px;
                }
                
                .volume-container {
                    padding: 3px 8px;
                }
                
                .song-details h5 {
                    font-size: 1rem;
                }
                
                .song-details p {
                    font-size: 0.8rem;
                }
            }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: var(--primary-color);">
        <div class="container-fluid px-3">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="logo.png" alt="Logo" class="navbar-logo me-2" style="height: 25px;">
                <span class="brand-text">فومیکو</span>
            </a>

            <!-- Mobile search toggle -->
            <button class="btn btn-link text-light d-lg-none me-2" type="button" data-bs-toggle="collapse" data-bs-target="#searchCollapse">
                <i class="fas fa-search"></i>
            </button>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Collapsible search bar for mobile -->
            <div class="collapse w-100" id="searchCollapse">
                <div class="mobile-search-container">
                    <form class="mobile-search-form">
                        <div class="search-input-wrapper">
                            <input 
                                type="search" 
                                class="form-control mobile-search-input" 
                                id="search-input-mobile" 
                                placeholder="جستجوی آهنگ..."
                                aria-label="Search songs"
                            >
                            <button 
                                class="mobile-search-btn" 
                                type="submit"
                                aria-label="Search"
                            >
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <style>
                    .mobile-search-container {
                        padding: 0.75rem;
                        background: rgba(255, 255, 255, 0.05);
                        border-radius: 8px;
                    }
                    
                    .mobile-search-form {
                        width: 100%;
                    }
                    
                    .search-input-wrapper {
                        position: relative;
                        display: flex;
                        align-items: center;
                    }
                    
                    .mobile-search-input {
                        background: rgba(255, 255, 255, 0.1);
                        border: none;
                        color: #fff;
                        padding-right: 40px;
                        border-radius: 25px;
                    }
                    
                    .mobile-search-input:focus {
                        background: rgba(255, 255, 255, 0.15);
                        box-shadow: none;
                    }
                    
                    .mobile-search-btn {
                        position: absolute;
                        right: 10px;
                        background: none;
                        border: none;
                        color: rgba(255, 255, 255, 0.7);
                        transition: color 0.2s ease;
                    }
                    
                    .mobile-search-btn:hover {
                        color: #fff;
                    }
                </style>
            </div>

            <!-- Main navbar content -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center ms-auto w-100">
                    <!-- Desktop search -->
                    <div class="search-box position-relative me-lg-3 d-none d-lg-block">
                        <div class="search-wrapper">
                            <input type="text" 
                                   class="form-control search-input" 
                                   id="search-input" 
                                   placeholder="جستجوی آهنگ..."
                                   autocomplete="off">
                            <button class="search-button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <style>
                            .search-box {
                                max-width: 300px;
                                width: 100%;
                            }
                            .search-wrapper {
                                display: flex;
                                align-items: center;
                                background: rgba(255, 255, 255, 0.1);
                                border-radius: 25px;
                                padding: 5px;
                            }
                            .search-input {
                                background: transparent !important;
                                border: none;
                                padding-right: 15px;
                                color: #fff !important;
                            }
                            .search-input:focus {
                                box-shadow: none;
                            }
                            .search-button {
                                background: none;
                                border: none;
                                color: rgba(255, 255, 255, 0.7);
                                padding: 0 15px;
                                transition: color 0.3s ease;
                            }
                            .search-button:hover {
                                color: #fff;
                            }
                        </style>
                    </div>
                    
                    <!-- Add song button -->
                    <button class="btn btn-add mt-2 mt-lg-0 w-100 w-lg-auto" id="add-song-btn">
                        <i class="fas fa-plus me-1"></i>
                        <span>افزودن آهنگ</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Spacer for fixed navbar -->
    <div style="height: 70px;"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="music-player">
                    <!-- Song List Section -->
                    <div class="song-list-section">
                        <h4 class="mb-3">پلی لیست شخصی من</h4>
                        
                        <!-- Player Controls - Moved here as requested -->
                        <div class="player-controls mb-4">
                            <div class="player-main">
                                <div class="current-song">
                                    <div class="album-art-container">
                                        <img src="default-album.jpg" alt="Album Art" class="current-song-cover">
                                        <div class="album-overlay"></div>
                                    </div>
                                    <div class="song-details">
                                        <h5 class="current-song-title">بدون آهنگ</h5>
                                        <p class="current-song-artist"><i class="fas fa-music me-1"></i>انتخاب کنید</p>
                                    </div>
                                </div>

                                <div class="progress-container">
                                    <div class="progress-bar"></div>
                                </div>
                                <div class="time-info">
                                    <span class="current-time">0:00</span>
                                    <span class="duration">0:00</span>
                                </div>

                                <div class="control-buttons">
                                    <button class="control-btn prev-btn" id="prev-btn" title="آهنگ قبلی">
                                        <i class="fas fa-backward-step"></i>
                                    </button>
                                    <button class="control-btn play-btn" id="play-btn" title="پخش/توقف">
                                        <i class="fas fa-play"></i>
                                    </button>
                                    <button class="control-btn next-btn" id="next-btn" title="آهنگ بعدی">
                                        <i class="fas fa-forward-step"></i>
                                    </button>
                                </div>

                                
                            </div>
                        </div>
                        
                        <div class="song-list">
                            <!-- آهنگ‌ها از دیتابیس بارگذاری می‌شوند -->
                            <div class="text-center p-4">در حال بارگذاری...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer mt-5 py-3" style="background-color: var(--primary-color);">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <img src="logo.png" alt="Logo" class="footer-logo me-2" style="height: 25px; width: auto;">
                        <span class="text-light fw-bold">فومیکو ۱۴۰۴ ©</span>
                   
                    <p class="text-light opacity-75 mb-0">
                          . تمامی حقوق محفوظ است.
                    </p>
                </div>
            </div>
        </div>
    </footer>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="app.js"></script>
</body>
</html>
