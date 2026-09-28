import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import persist from '@alpinejs/persist';
import Chart from 'chart.js/auto';

Alpine.plugin(collapse);
Alpine.plugin(focus);
Alpine.plugin(persist);

window.Alpine = Alpine;
window.Chart = Chart;

// ============================================
// Theme Toggle
// ============================================
window.toggleTheme = () => {
    const html = document.documentElement;
    const current = html.getAttribute('data-theme');
    const newTheme = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateThemeButton(newTheme);
    return newTheme;
};

// ============================================
// Create Floating Theme Button
// ============================================
function createThemeButton() {
    // تجنب التكرار
    if (document.getElementById('floating-theme-btn')) return;

    const btn = document.createElement('button');
    btn.id = 'floating-theme-btn';
    btn.setAttribute('aria-label', 'تبديل الوضع الليلي');
    btn.setAttribute('title', 'تبديل الوضع الليلي');
    btn.style.cssText = `
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 9999;
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0F1E3C, #1E3A5F);
        border: 2px solid rgba(212, 162, 76, 0.5);
        color: #E5B968;
        font-size: 1.3rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 24px rgba(15, 30, 60, 0.35);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-family: 'Cairo', sans-serif;
    `;

    btn.addEventListener('mouseenter', () => {
        btn.style.transform = 'scale(1.1) translateY(-3px)';
        btn.style.boxShadow = '0 12px 32px rgba(212, 162, 76, 0.5)';
    });

    btn.addEventListener('mouseleave', () => {
        btn.style.transform = 'scale(1)';
        btn.style.boxShadow = '0 8px 24px rgba(15, 30, 60, 0.35)';
    });

    btn.addEventListener('click', () => {
        window.toggleTheme();
    });

    document.body.appendChild(btn);

    // ضبط الأيقونة حسب الثيم الحالي
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
    updateThemeButton(currentTheme);
}

function updateThemeButton(theme) {
    const btn = document.getElementById('floating-theme-btn');
    if (!btn) return;

    if (theme === 'dark') {
        btn.innerHTML = '<i class="fa-solid fa-sun"></i>';
        btn.style.background = 'linear-gradient(135deg, #D4A24C, #B8873A)';
        btn.style.color = '#FFFFFF';
    } else {
        btn.innerHTML = '<i class="fa-solid fa-moon"></i>';
        btn.style.background = 'linear-gradient(135deg, #0F1E3C, #1E3A5F)';
        btn.style.color = '#E5B968';
    }
}

// ============================================
// Toast Notifications
// ============================================
window.showToast = (message, type = 'info') => {
    const colors = {
        success: 'bg-emerald-500',
        error: 'bg-red-500',
        warning: 'bg-amber-500',
        info: 'bg-blue-500',
    };
    const toast = document.createElement('div');
    toast.className = `fixed top-5 left-5 z-[9999] ${colors[type]} text-white px-5 py-3 rounded-2xl shadow-lift font-cairo font-bold`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
};

// ============================================
// Alpine Components
// ============================================
document.addEventListener('alpine:init', () => {
    Alpine.data('counter', (target = 0, duration = 1500) => ({
        current: 0,
        init() {
            const step = target / (duration / 16);
            const timer = setInterval(() => {
                this.current += step;
                if (this.current >= target) {
                    this.current = target;
                    clearInterval(timer);
                }
            }, 16);
        },
        get formatted() {
            return Math.floor(this.current).toLocaleString('ar-EG');
        }
    }));

    Alpine.data('progressBar', (value = 0) => ({
        value: 0,
        init() {
            setTimeout(() => { this.value = value; }, 100);
        }
    }));
});

Alpine.start();

// ============================================
// Initialize on DOM Load
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme') || 'dark';   // 👈 الافتراضي
    document.documentElement.setAttribute('data-theme', savedTheme);
    createThemeButton();
});

