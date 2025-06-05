// متغیرهای سراسری
let songs = [];
let currentSongIndex = 0;
let isPlaying = false;
let audio = new Audio();

// تنظیم رویدادهای صفحه در هنگام بارگذاری
document.addEventListener('DOMContentLoaded', function() {
    // بارگذاری آهنگ‌ها از دیتابیس
    loadSongs();
    
    // تنظیم رویدادهای دکمه‌های پخش
    document.getElementById('play-btn').addEventListener('click', togglePlay);
    document.getElementById('prev-btn').addEventListener('click', playPrevious);
    document.getElementById('next-btn').addEventListener('click', playNext);
    
    // تنظیم رویداد دکمه افزودن آهنگ
    document.getElementById('add-song-btn').addEventListener('click', showAddSongForm);
    
    // تنظیم رویداد جستجو
    document.getElementById('search-input').addEventListener('input', searchSongs);
    document.getElementById('search-input-mobile').addEventListener('input', searchSongs);
    
    // تنظیم رویدادهای صوتی
    audio.addEventListener('timeupdate', updateProgress);
    audio.addEventListener('ended', playNext);
    
    // تنظیم رویداد نوار پیشرفت
    document.querySelector('.progress-container').addEventListener('click', setProgress);
});

// بارگذاری آهنگ‌ها از دیتابیس
function loadSongs() {
    fetch('api.php?action=get_songs')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.songs) {
                songs = data.songs;
                renderSongList();
                if (songs.length > 0) {
                    loadSong(0);
                }
            } else {
                showMessage('خطا در بارگذاری آهنگ‌ها: ' + (data.error || 'خطای نامشخص'), 'error');
            }
        })
        .catch(error => {
            showMessage('خطا در ارتباط با سرور: ' + error.message, 'error');
        });
}

// نمایش لیست آهنگ‌ها
function renderSongList() {
    const songListContainer = document.querySelector('.song-list');
    songListContainer.innerHTML = '';
    
    if (songs.length === 0) {
        songListContainer.innerHTML = '<div class="text-center p-4">هیچ آهنگی یافت نشد</div>';
        return;
    }
    
    songs.forEach((song, index) => {
        const songItem = document.createElement('div');
        songItem.className = `song-item ${index === currentSongIndex ? 'active' : ''}`;
        songItem.dataset.index = index;
        
        const imageUrl = song.image_url || 'default-album.jpg';
        
        songItem.innerHTML = `
            <img src="${imageUrl}" alt="${song.title}" class="song-cover" onerror="this.src='default-album.jpg'">
            <div class="song-info">
                <div class="song-title">${song.title}</div>
                <div class="song-artist">${song.artist}</div>
            </div>
            <div class="song-actions">
                <button class="play-song" data-index="${index}" title="پخش">
                    <i class="fas fa-play"></i>
                </button>
                <button class="delete-song" data-id="${song.id}" title="حذف">
                    <i class="fas fa-trash"></i>
                </button>
                <button class="download-song" data-url="${song.audio_url}" title="دانلود">
                    <i class="fas fa-download"></i>
                </button>
            </div>
        `;
        
        songListContainer.appendChild(songItem);
    });
    
    // تنظیم رویدادهای دکمه‌های آهنگ‌ها
    document.querySelectorAll('.play-song').forEach(button => {
        button.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            loadSong(index);
            playAudio();
        });
    });
    

    
    document.querySelectorAll('.delete-song').forEach(button => {
        button.addEventListener('click', function() {
            const songId = parseInt(this.dataset.id);
            deleteSong(songId);
        });
    });
    
    document.querySelectorAll('.download-song').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const audioUrl = this.dataset.url;
            if (audioUrl) {
                // ایجاد یک لینک موقت برای دانلود
                const a = document.createElement('a');
                a.href = audioUrl;
                a.download = audioUrl.split('/').pop(); // استخراج نام فایل از URL
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            } else {
                showMessage('آدرس فایل صوتی نامعتبر است', 'error');
            }
        });
    });
    
    // تنظیم رویداد کلیک روی آیتم آهنگ
    document.querySelectorAll('.song-item').forEach(item => {
        item.addEventListener('click', function(e) {
            // اگر روی دکمه‌های عملیات کلیک نشده باشد
            if (!e.target.closest('.song-actions')) {
                const index = parseInt(this.dataset.index);
                loadSong(index);
                playAudio();
            }
        });
    });
}

// بارگذاری آهنگ
function loadSong(index) {
    if (index < 0 || index >= songs.length) return;
    
    currentSongIndex = index;
    const song = songs[index];
    
    // به‌روزرسانی اطلاعات آهنگ فعلی
    document.querySelector('.current-song-title').textContent = song.title;
    document.querySelector('.current-song-artist').textContent = song.artist;
    
    const albumArt = document.querySelector('.current-song-cover');
    albumArt.src = song.image_url || 'default-album.jpg';
    albumArt.alt = song.title;
    
    // اضافه کردن رویداد برای زمانی که تصویر بارگذاری نشود
    albumArt.onerror = function() {
        this.src = 'default-album.jpg';
    };
    
    // تنظیم منبع صوتی
    audio.src = song.audio_url;
    audio.load();
    
    // به‌روزرسانی کلاس فعال در لیست آهنگ‌ها
    document.querySelectorAll('.song-item').forEach((item, i) => {
        item.classList.toggle('active', i === index);
    });
    
    // به‌روزرسانی آیکون دکمه پخش/توقف
    updatePlayPauseIcon();
}

// پخش/توقف آهنگ
function togglePlay() {
    if (songs.length === 0) return;
    
    if (isPlaying) {
        pauseAudio();
    } else {
        playAudio();
    }
}

// پخش آهنگ
function playAudio() {
    audio.play();
    isPlaying = true;
    updatePlayPauseIcon();
}

// توقف آهنگ
function pauseAudio() {
    audio.pause();
    isPlaying = false;
    updatePlayPauseIcon();
}

// به‌روزرسانی آیکون دکمه پخش/توقف
function updatePlayPauseIcon() {
    const playBtn = document.getElementById('play-btn');
    playBtn.innerHTML = isPlaying ? '<i class="fas fa-pause"></i>' : '<i class="fas fa-play"></i>';
}

// پخش آهنگ قبلی
function playPrevious() {
    if (songs.length === 0) return;
    
    let prevIndex = currentSongIndex - 1;
    if (prevIndex < 0) prevIndex = songs.length - 1;
    
    loadSong(prevIndex);
    if (isPlaying) playAudio();
}

// پخش آهنگ بعدی
function playNext() {
    if (songs.length === 0) return;
    
    let nextIndex = currentSongIndex + 1;
    if (nextIndex >= songs.length) nextIndex = 0;
    
    loadSong(nextIndex);
    if (isPlaying) playAudio();
}

// به‌روزرسانی نوار پیشرفت
function updateProgress() {
    const duration = audio.duration;
    const currentTime = audio.currentTime;
    
    if (duration) {
        // به‌روزرسانی نوار پیشرفت
        const progressPercent = (currentTime / duration) * 100;
        document.querySelector('.progress-bar').style.width = `${progressPercent}%`;
        
        // به‌روزرسانی زمان‌ها
        document.querySelector('.current-time').textContent = formatTime(currentTime);
        document.querySelector('.duration').textContent = formatTime(duration);
    } else {
        document.querySelector('.current-time').textContent = '0:00';
        document.querySelector('.duration').textContent = '0:00';
    }
}

// تنظیم پیشرفت پخش با کلیک روی نوار
function setProgress(e) {
    const width = this.clientWidth;
    const clickX = e.offsetX;
    const duration = audio.duration;
    
    if (duration) {
        audio.currentTime = (clickX / width) * duration;
    }
}

// تبدیل ثانیه به فرمت mm:ss
function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${minutes}:${secs < 10 ? '0' : ''}${secs}`;
}

// جستجوی آهنگ‌ها
function searchSongs(e) {
    const searchTerm = e.target.value.toLowerCase();
    
    if (searchTerm === '') {
        // نمایش همه آهنگ‌ها
        document.querySelectorAll('.song-item').forEach(item => {
            item.style.display = 'flex';
        });
        return;
    }
    
    // فیلتر کردن آهنگ‌ها بر اساس عبارت جستجو
    document.querySelectorAll('.song-item').forEach(item => {
        const index = parseInt(item.dataset.index);
        const song = songs[index];
        
        if (song.title.toLowerCase().includes(searchTerm) || 
            song.artist.toLowerCase().includes(searchTerm) ||
            (song.album && song.album.toLowerCase().includes(searchTerm)) ||
            (song.genre && song.genre.toLowerCase().includes(searchTerm))) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

// نمایش فرم افزودن آهنگ
function showAddSongForm() {
    // بررسی وجود فرم قبلی و حذف آن
    const existingForm = document.querySelector('.add-song-form');
    if (existingForm) {
        existingForm.remove();
        return;
    }
    
    const formContainer = document.createElement('div');
    formContainer.className = 'add-song-form';
    formContainer.innerHTML = `
        <div class="edit-form-header">
            <h5 class="edit-form-title">افزودن آهنگ جدید</h5>
            <button type="button" class="edit-form-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="add-song-form">
            <div class="form-group mb-3">
                <label for="title">عنوان آهنگ</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group mb-3">
                <label for="artist">خواننده</label>
                <input type="text" class="form-control" id="artist" name="artist" required>
            </div>
            <div class="form-group mb-3">
                <label for="image_url">آدرس تصویر</label>
                <input type="text" class="form-control" id="image_url" name="image_url">
            </div>
            <div class="form-group mb-3">
                <label for="audio_url">آدرس فایل صوتی</label>
                <input type="text" class="form-control" id="audio_url" name="audio_url" required>
            </div>
            <div class="form-group mb-3">
                <label for="album">آلبوم</label>
                <input type="text" class="form-control" id="album" name="album">
            </div>
            <div class="form-group mb-3">
                <label for="genre">سبک</label>
                <input type="text" class="form-control" id="genre" name="genre">
            </div>
            <div class="edit-buttons">
                <button type="submit" class="btn btn-save">ذخیره</button>
                <button type="button" class="btn btn-cancel">انصراف</button>
            </div>
        </form>
    `;
    
    // افزودن فرم به صفحه
    document.querySelector('.music-player').appendChild(formContainer);
    
    // تنظیم رویدادها
    document.querySelector('.edit-form-close').addEventListener('click', () => {
        formContainer.remove();
    });
    
    document.querySelector('.btn-cancel').addEventListener('click', () => {
        formContainer.remove();
    });
    
    document.getElementById('add-song-form').addEventListener('submit', function(e) {
        e.preventDefault();
        addSong(this);
    });
}

// افزودن آهنگ جدید
function addSong(form) {
    const formData = new FormData(form);
    
    fetch('api.php?action=add_song', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            // افزودن آهنگ جدید به آرایه آهنگ‌ها
            songs.push(data.song);
            renderSongList();
            // حذف فرم
            document.querySelector('.add-song-form').remove();
        } else {
            showMessage(data.error || 'خطا در افزودن آهنگ', 'error');
        }
    })
    .catch(error => {
        showMessage('خطا در ارتباط با سرور: ' + error.message, 'error');
    });
}



// حذف آهنگ
function deleteSong(songId) {
    if (!confirm('آیا از حذف این آهنگ اطمینان دارید؟')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('id', songId);
    
    fetch('api.php?action=delete_song', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            
            // حذف آهنگ از آرایه آهنگ‌ها
            const index = songs.findIndex(s => s.id === songId);
            if (index !== -1) {
                songs.splice(index, 1);
                
                // اگر آهنگ در حال پخش حذف شده است
                if (index === currentSongIndex) {
                    if (songs.length > 0) {
                        // بارگذاری آهنگ بعدی یا قبلی
                        const newIndex = index < songs.length ? index : index - 1;
                        loadSong(newIndex >= 0 ? newIndex : 0);
                    } else {
                        // اگر هیچ آهنگی باقی نمانده است
                        audio.pause();
                        isPlaying = false;
                        document.querySelector('.current-song-title').textContent = 'بدون آهنگ';
                        document.querySelector('.current-song-artist').textContent = '';
                        document.querySelector('.current-song-cover').src = 'default-album.jpg';
                        document.querySelector('.progress-bar').style.width = '0%';
                        document.querySelector('.current-time').textContent = '0:00';
                        document.querySelector('.duration').textContent = '0:00';
                        updatePlayPauseIcon();
                    }
                } else if (index < currentSongIndex) {
                    // اگر آهنگ حذف شده قبل از آهنگ در حال پخش بوده است
                    currentSongIndex--;
                }
                
                renderSongList();
            }
        } else {
            showMessage(data.error || 'خطا در حذف آهنگ', 'error');
        }
    })
    .catch(error => {
        showMessage('خطا در ارتباط با سرور: ' + error.message, 'error');
    });
}

// نمایش پیام
function showMessage(message, type = 'info') {
    // بررسی وجود المان پیام قبلی و حذف آن
    const existingMessage = document.querySelector('.message-container');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // ایجاد المان پیام
    const messageContainer = document.createElement('div');
    messageContainer.className = `message-container message-${type}`;
    messageContainer.innerHTML = `
        <div class="message-content">
            <i class="message-icon fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
            <span class="message-text">${message}</span>
        </div>
        <button class="message-close">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // افزودن استایل
    const style = document.createElement('style');
    style.textContent = `
        .message-container {
            position: fixed;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            min-width: 300px;
            max-width: 80%;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideDown 0.3s ease;
        }
        
        .message-success {
            background-color: rgba(46, 204, 113, 0.9);
            color: white;
        }
        
        .message-error {
            background-color: rgba(231, 76, 60, 0.9);
            color: white;
        }
        
        .message-info {
            background-color: rgba(52, 152, 219, 0.9);
            color: white;
        }
        
        .message-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .message-icon {
            font-size: 1.2rem;
        }
        
        .message-close {
            background: none;
            border: none;
            color: white;
            opacity: 0.7;
            cursor: pointer;
            transition: opacity 0.2s ease;
        }
        
        .message-close:hover {
            opacity: 1;
        }
        
        @keyframes slideDown {
            from { transform: translate(-50%, -20px); opacity: 0; }
            to { transform: translate(-50%, 0); opacity: 1; }
        }
    `;
    
    // افزودن به صفحه
    document.head.appendChild(style);
    document.body.appendChild(messageContainer);
    
    // تنظیم رویداد دکمه بستن
    messageContainer.querySelector('.message-close').addEventListener('click', () => {
        messageContainer.remove();
    });
    
    // حذف خودکار پیام بعد از 5 ثانیه
    setTimeout(() => {
        if (messageContainer.parentNode) {
            messageContainer.remove();
        }
    }, 5000);
}