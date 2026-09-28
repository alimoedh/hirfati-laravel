// ============================================

// نظام الإشعارات
function showNotification(message, type = 'info') {
    const colors = { info: '#3B82F6', success: '#10B981', warning: '#F59E0B', danger: '#EF4444' };
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed; top: 20px; left: 20px; padding: 15px 25px;
        background: ${colors[type]}; color: white; border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 9999;
        font-family: 'Cairo', sans-serif; animation: slideIn 0.3s ease; max-width: 400px;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => { notification.style.opacity = '0'; notification.style.transition = 'opacity 0.3s'; setTimeout(() => notification.remove(), 300); }, 5000);
}

// تحديث نقاط الولاء
function updateLoyaltyPoints(points) {
    const pointsElement = document.getElementById('loyalty-points');
    if (pointsElement) {
        pointsElement.textContent = points;
        pointsElement.style.animation = 'none';
        setTimeout(() => { pointsElement.style.animation = 'bounce 0.5s ease'; }, 10);
    }
}

// تبديل نوع الحساب
function selectRole(role) {
    const roleInput = document.getElementById('role-input');
    const btnClient = document.getElementById('btn-client');
    const btnCraftsman = document.getElementById('btn-craftsman');
    const craftsmanFields = document.getElementById('craftsman-fields');
    if (roleInput) roleInput.value = role;
    if (role === 'craftsman') {
        btnCraftsman?.classList.add('active');
        btnClient?.classList.remove('active');
        if (craftsmanFields) craftsmanFields.style.display = 'block';
    } else {
        btnClient?.classList.add('active');
        btnCraftsman?.classList.remove('active');
        if (craftsmanFields) craftsmanFields.style.display = 'none';
    }
}

// إرسال رسالة AJAX
function sendMessageAjax(requestId, message, receiverId) {
    fetch('../api/send_message.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `request_id=${requestId}&message=${encodeURIComponent(message)}&receiver_id=${receiverId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const chatBody = document.getElementById('chatBody');
            const msgDiv = document.createElement('div');
            msgDiv.className = 'msg sent';
            msgDiv.textContent = message;
            chatBody.appendChild(msgDiv);
            chatBody.scrollTop = chatBody.scrollHeight;
            document.getElementById('msgInput').value = '';
        }
    })
    .catch(error => console.error('Error:', error));
}

// البث المباشر (WebRTC)
let localStream = null;
function startLiveStream() {
    navigator.mediaDevices.getUserMedia({ video: true, audio: true })
    .then(stream => {
        localStream = stream;
        const videoElement = document.getElementById('localVideo');
        if (videoElement) videoElement.srcObject = stream;
        showNotification('تم بدء البث المباشر', 'success');
    })
    .catch(error => { console.error('Error:', error); showNotification('تعذر الوصول إلى الكاميرا والميكروفون', 'danger'); });
}
function stopLiveStream() {
    if (localStream) { localStream.getTracks().forEach(track => track.stop()); localStream = null; const videoElement = document.getElementById('localVideo'); if (videoElement) videoElement.srcObject = null; showNotification('تم إيقاف البث المباشر', 'info'); }
}

// الواقع المعزز (AR)
function initAR() {
    if (typeof AFRAME !== 'undefined') { console.log('AR initialized'); }
    else { const script = document.createElement('script'); script.src = 'https://cdn.jsdelivr.net/npm/ar.js@2.2.2/aframe/build/aframe-ar.min.js'; document.head.appendChild(script); }
}

// تشغيل فيديو تعليمي
function playTutorial(videoId) {
    const player = document.getElementById('videoPlayer');
    if (player) { player.src = `https://www.youtube.com/embed/${videoId}`; player.scrollIntoView({ behavior: 'smooth' }); }
}

// تحميل الرسم البياني (Chart.js)
function loadChart(chartId, data, labels, title, type = 'bar') {
    if (typeof Chart === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
        script.onload = function() { createChart(chartId, data, labels, title, type); };
        document.head.appendChild(script);
    } else { createChart(chartId, data, labels, title, type); }
}
function createChart(chartId, data, labels, title, type) {
    const ctx = document.getElementById(chartId);
    if (!ctx) return;
    new Chart(ctx, {
        type: type,
        data: {
            labels: labels,
            datasets: [{
                label: title,
                data: data,
                backgroundColor: ['rgba(217,119,6,0.6)','rgba(16,185,129,0.6)','rgba(59,130,246,0.6)','rgba(239,68,68,0.6)','rgba(168,85,247,0.6)'],
                borderColor: ['#D97706','#10B981','#3B82F6','#EF4444','#A855F7'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { labels: { font: { family: 'Cairo' } } } },
            scales: { y: { beginAtZero: true } }
        }
    });
}

// تبديل الوضع المظلم
function toggleDarkMode() {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
}

// تحميل الوضع المحفوظ
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    const btn = document.createElement('button');
    btn.className = 'dark-toggle';
    btn.style.cssText = `
        position: fixed; bottom: 20px; right: 20px; z-index: 9999;
        background: var(--card); border: 1px solid #E2E8F0; border-radius: 50%;
        width: 50px; height: 50px; font-size: 1.5rem; cursor: pointer;
        box-shadow: var(--shadow); transition: var(--transition);
    `;
    btn.innerHTML = savedTheme === 'dark' ? '☀️' : '🌙';
    btn.onclick = function() {
        toggleDarkMode();
        this.innerHTML = document.documentElement.getAttribute('data-theme') === 'dark' ? '☀️' : '🌙';
    };
    document.body.appendChild(btn);
});

// Service Worker للتطبيق
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/hirfati/assets/js/sw.js')
        .then(registration => console.log('Service Worker registered'))
        .catch(error => console.log('Service Worker registration failed:', error));
}

console.log('✅ منصة حرفتي 2.0 جاهزة!');
