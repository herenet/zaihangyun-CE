<script>
    // 滚动时导航栏样式变化
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 100) {
            navbar.style.background = 'rgba(255, 255, 255, 0.98)';
            navbar.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
        } else {
            navbar.style.background = 'rgba(255, 255, 255, 0.95)';
            navbar.style.boxShadow = 'none';
        }
    });

    // 平滑滚动到锚点
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // 监听滚动，添加动画效果
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // 为所有卡片添加滚动动画
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.feature-card, .module-card, .pricing-card');
        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });
    });

    // 移动端菜单控制
    function toggleMobileMenu() {
        const mobileMenu = document.getElementById('mobile-menu');
        const menuToggle = document.querySelector('.mobile-menu-toggle');
        
        if (mobileMenu.classList.contains('active')) {
            mobileMenu.classList.remove('active');
            menuToggle.classList.remove('active');
        } else {
            mobileMenu.classList.add('active');
            menuToggle.classList.add('active');
        }
    }

    // 点击菜单项后关闭移动端菜单
    document.querySelectorAll('.mobile-nav-links a').forEach(link => {
        link.addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            const menuToggle = document.querySelector('.mobile-menu-toggle');
            mobileMenu.classList.remove('active');
            menuToggle.classList.remove('active');
        });
    });

    // 悬浮支持面板
    function toggleSupportPanel() {
        const panel = document.getElementById('support-panel');
        if (panel) {
            panel.style.display = panel.style.display === 'none' || panel.style.display === '' ? 'block' : 'none';
        }
    }

    // 点击其他地方关闭面板
    document.addEventListener('click', function(e) {
        const support = document.getElementById('floating-support');
        const panel = document.getElementById('support-panel');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuToggle = document.querySelector('.mobile-menu-toggle');
        
        // 关闭支持面板
        if (support && panel && !support.contains(e.target)) {
            panel.style.display = 'none';
        }
        
        // 关闭移动端菜单
        if (mobileMenu && menuToggle && !menuToggle.contains(e.target) && !mobileMenu.contains(e.target)) {
            mobileMenu.classList.remove('active');
            menuToggle.classList.remove('active');
        }
    });

    var _hmt = _hmt || [];
    (function() {
    var hm = document.createElement("script");
    hm.src = "https://hm.baidu.com/hm.js?d7aeb4cc00ca36d316316ac118ee2298";
    var s = document.getElementsByTagName("script")[0]; 
    s.parentNode.insertBefore(hm, s);
    })();

</script> 