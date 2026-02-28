<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>码上导航</title>
    <link rel="icon" type="image/png" href="logo.png">
    <link rel="shortcut icon" type="image/png" href="logo.png">
    <link rel="apple-touch-icon" href="logo.png">
    <link rel="stylesheet" href="style.css">
    <style>
        /* 隐藏滚动条 */
        #categories::-webkit-scrollbar,
        .flex-1.bg-gray-100::-webkit-scrollbar {
            width: 0;
            height: 0;
        }
        #categories,
        .flex-1.bg-gray-100 {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
    </style>
</head>
<body>
    <div class="flex h-screen overflow-hidden">
        <!-- 左侧边栏 -->
        <div class="w-56 bg-white border-r flex-col" style="display:flex;flex-direction:column;height:100vh;">
            <div class="h-16 flex items-center px-6 border-b" style="flex-shrink:0;">
                <div class="flex items-center space-x-3">
                    <img src="logo.png" alt="Logo" class="w-10 h-10 rounded-full object-cover" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold" style="display:none;">M</div>
                    <span class="text-gray-800 font-semibold text-base" style="text-decoration:none;">码上导航</span>
                </div>
            </div>
            <div id="categories" class="flex-1 overflow-y-auto py-2" style="flex:1;overflow-y:auto;"></div>
        </div>

        <!-- 右侧内容 -->
        <div class="flex-1 flex flex-col" style="display:flex;flex-direction:column;height:100vh;">
            <div class="h-16 bg-white border-b flex items-center justify-center px-6" style="flex-shrink:0;">
                <div class="text-center">
                    <span style="font-size:1rem;font-weight:600;color:#1f2937;text-decoration:none;">码上导航</span>
                    <span style="font-size:1rem;font-weight:600;color:#1f2937;margin-left:0.75rem;text-decoration:none;">始于2026</span>
                </div>
            </div>
            
            <div class="flex-1 bg-gray-100 overflow-y-auto p-6" style="flex:1;overflow-y:auto;">
                <div id="password-lock" style="display:none;height:100%;" class="flex items-center justify-center">
                    <div class="bg-white rounded-lg p-8 shadow-md" style="max-width:28rem;width:100%;">
                        <div class="text-center mb-6">
                            <div style="width:4rem;height:4rem;background:#3b82f6;border-radius:50%;margin:0 auto 1rem;display:flex;align-items:center;justify-content:center;">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </div>
                            <h3 style="font-size:1.25rem;font-weight:700;color:#1f2937;margin-bottom:0.5rem;text-align:center;">此分类已加密</h3>
                            <p class="text-gray-500 text-sm" style="text-align:center;">请输入密码以查看内容</p>
                        </div>
                        <form id="password-form" style="text-align:center;">
                            <input type="password" id="password-input" placeholder="请输入访问密码" class="mb-2" autofocus style="text-align:center;">
                            <p id="password-error" class="text-red-500 text-xs mb-3" style="display:none;text-align:center;"></p>
                            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg">解锁查看</button>
                        </form>
                    </div>
                </div>
                
                <div id="sites" class="grid gap-4" style="grid-template-columns:repeat(auto-fill,minmax(220px,1fr));"></div>
            </div>
        </div>
    </div>

    <!-- 复制提示 -->
    <div id="copy-toast" style="display:none;" class="fixed top-1/2 left-1/2 transform bg-black bg-opacity-80 text-white px-6 py-3 rounded-lg z-50">
        <div class="flex items-center space-x-2">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            <span>链接已复制</span>
        </div>
    </div>

    <script>
    let categories = [];
    let selectedCategoryId = null;
    let unlockedCategories = new Set();

    async function loadCategories() {
        const res = await fetch('api.php?action=categories');
        categories = await res.json();
        renderCategories();
        if (categories.length > 0) {
            selectCategory(categories[0].id);
        }
    }

    function renderCategories() {
        const html = categories.map(cat => `
            <button onclick="selectCategory(${cat.id})" 
                    style="width:100%;padding:0.75rem 1.5rem;text-align:left;font-size:0.875rem;background:${selectedCategoryId == cat.id ? '#e3f2fd' : 'transparent'};color:${selectedCategoryId == cat.id ? '#1976d2' : '#666'};border:none;cursor:pointer;text-decoration:none;">
                ${cat.name}
            </button>
        `).join('');
        document.getElementById('categories').innerHTML = html;
    }

    async function selectCategory(id) {
        selectedCategoryId = id;
        renderCategories();
        
        const category = categories.find(c => c.id == id);
        if (category.is_password_enabled == 1 && !unlockedCategories.has(id)) {
            document.getElementById('password-lock').style.display = 'flex';
            document.getElementById('sites').style.display = 'none';
            document.getElementById('password-input').value = '';
            document.getElementById('password-error').style.display = 'none';
        } else {
            document.getElementById('password-lock').style.display = 'none';
            document.getElementById('sites').style.display = 'grid';
            loadSites(id);
        }
    }

    // 生成随机颜色（基于哈希值，确保同一网站颜色固定）
    function getRandomColor(seed) {
        const colors = [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8',
            '#F7DC6F', '#BB8FCE', '#85C1E2', '#F8B739', '#52B788',
            '#E74C3C', '#3498DB', '#9B59B6', '#1ABC9C', '#F39C12',
            '#E67E22', '#16A085', '#27AE60', '#2980B9', '#8E44AD'
        ];
        const index = Math.abs(seed) % colors.length;
        return colors[index];
    }

    // 简单哈希函数
    function hashCode(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }
        return hash;
    }

    async function loadSites(categoryId) {
        const res = await fetch(`api.php?action=sites&categoryId=${categoryId}`);
        const sites = await res.json();
        
        if (sites.length === 0) {
            document.getElementById('sites').innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:5rem 0;color:#9ca3af;">暂无网站</div>';
            return;
        }
        
        const html = sites.map(site => {
            const firstChar = site.title.charAt(0).toUpperCase();
            const color = getRandomColor(hashCode(site.title + site.id));
            return `
                <div class="bg-white rounded-lg p-4 flex items-start space-x-3 relative group">
                    <a href="${site.url}" target="_blank" class="flex items-start space-x-3 flex-1 min-w-0">
                        <div class="w-10 h-10 rounded flex-shrink-0 flex items-center justify-center text-white font-bold text-sm" style="background:${color};">
                            ${firstChar}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-medium text-gray-800 text-sm mb-1 truncate">${site.title}</h3>
                            ${site.description ? `<p class="text-xs text-gray-500" style="font-size:0.6875rem;line-height:1.3;word-wrap:break-word;overflow-wrap:break-word;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">${site.description}</p>` : ''}
                        </div>
                    </a>
                    <button onclick="copyUrl(event, '${encodeURIComponent(site.url)}')" class="absolute top-2 right-2 w-8 h-8 bg-blue-500 text-white rounded flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity" title="复制链接">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                        </svg>
                    </button>
                </div>
            `;
        }).join('');
        document.getElementById('sites').innerHTML = html;
    }

    async function copyUrl(e, encodedUrl) {
        e.preventDefault();
        e.stopPropagation();
        const url = decodeURIComponent(encodedUrl);
        try {
            await navigator.clipboard.writeText(url);
            const toast = document.getElementById('copy-toast');
            toast.style.display = 'block';
            setTimeout(() => toast.style.display = 'none', 2000);
        } catch (err) {
            // 降级方案：使用传统方法复制
            const textarea = document.createElement('textarea');
            textarea.value = url;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            
            const toast = document.getElementById('copy-toast');
            toast.style.display = 'block';
            setTimeout(() => toast.style.display = 'none', 2000);
        }
    }

    document.getElementById('password-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const password = document.getElementById('password-input').value;
        
        const res = await fetch('api.php?action=verify_password', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({categoryId: selectedCategoryId, password})
        });
        
        const data = await res.json();
        if (data.success) {
            unlockedCategories.add(selectedCategoryId);
            document.getElementById('password-lock').style.display = 'none';
            document.getElementById('sites').style.display = 'grid';
            loadSites(selectedCategoryId);
        } else {
            const error = document.getElementById('password-error');
            error.textContent = '密码错误，请重试';
            error.style.display = 'block';
        }
    });

    loadCategories();
    </script>
</body>
</html>
