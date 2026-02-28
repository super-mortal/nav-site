<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台管理 - 码上导航</title>
    <link rel="icon" type="image/png" href="logo.png">
    <link rel="shortcut icon" type="image/png" href="logo.png">
    <link rel="apple-touch-icon" href="logo.png">
    <link rel="stylesheet" href="style.css">
    <style>
    .admin-container { display: flex; height: 100vh; overflow: hidden; }
    .sidebar { width: 13rem; background: white; border-right: 1px solid #e5e7eb; }
    .main-content { flex: 1; display: flex; flex-direction: column; }
    /* 隐藏滚动条 */
    .sidebar > div:last-child::-webkit-scrollbar,
    .main-content > div:last-child::-webkit-scrollbar { width: 0; height: 0; }
    .sidebar > div:last-child,
    .main-content > div:last-child { scrollbar-width: none; -ms-overflow-style: none; }
    .category-item { padding: 0.5rem; border-radius: 0.5rem; cursor: pointer; margin-bottom: 0.375rem; border: 2px solid transparent; }
    .category-item.active { background: #eff6ff; border-color: #3b82f6; }
    .category-item:not(.active) { background: #f9fafb; }
    .category-item:not(.active):hover { border-color: #d1d5db; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 0.5rem 0.75rem; text-align: left; font-size: 0.8125rem; }
    th { background: #f9fafb; font-weight: 600; color: #374151; }
    tbody tr { border-bottom: 1px solid #e5e7eb; }
    tbody tr:hover { background: #f9fafb; }
    .icon-box { width: 1.5rem; height: 1.5rem; border-radius: 0.25rem; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.6875rem; }
    .btn-icon { width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; border-radius: 0.375rem; transition: all 0.2s; cursor: pointer; border: none; background: none; }
    .btn-edit { color: #3b82f6; }
    .btn-edit:hover { background: #dbeafe; }
    .btn-delete { color: #ef4444; }
    .btn-delete:hover { background: #fee2e2; }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- 左侧分类 -->
        <div class="sidebar" style="display:flex;flex-direction:column;height:100vh;">
            <div style="height:3rem;padding:0 1rem;border-bottom:1px solid #e5e7eb;flex-shrink:0;display:flex;align-items:center;">
                <h1 style="font-size:1rem;font-weight:700;color:#111827;text-decoration:none;line-height:1;">后台管理</h1>
            </div>
            
            <div style="padding:0.75rem;flex:1;overflow-y:auto;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;">
                    <h2 class="text-sm font-semibold text-gray-700" style="text-decoration:none;font-size:0.8125rem;">分类列表</h2>
                    <button onclick="showCategoryModal()" style="color:#3b82f6;font-weight:500;font-size:0.8125rem;border:none;background:none;cursor:pointer;text-decoration:none;">+ 添加</button>
                </div>
                <div id="categories"></div>
            </div>
        </div>

        <!-- 右侧网站管理 -->
        <div class="main-content" style="display:flex;flex-direction:column;height:100vh;">
            <div id="header" style="height:3rem;background:white;border-bottom:1px solid #e5e7eb;padding:0 1rem;display:none;flex-shrink:0;">
                <div style="display:flex;justify-content:space-between;align-items:center;height:100%;">
                    <h2 id="category-title" style="font-size:1rem;font-weight:700;color:#111827;text-decoration:none;line-height:1;"></h2>
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <button onclick="editCategory(selectedCategoryId)" class="btn" style="background:#3b82f6;color:white;text-decoration:none;padding:0.5rem 0.875rem;font-size:0.875rem;flex-shrink:0;border:none;cursor:pointer;border-radius:0.5rem;font-weight:500;">编辑分类</button>
                        <button onclick="deleteCategory(selectedCategoryId)" class="btn" style="background:#ef4444;color:white;text-decoration:none;padding:0.5rem 0.875rem;font-size:0.875rem;flex-shrink:0;border:none;cursor:pointer;border-radius:0.5rem;font-weight:500;">删除分类</button>
                        <button onclick="showSiteModal()" class="btn btn-primary" style="background:#10b981;text-decoration:none;padding:0.5rem 0.875rem;font-size:0.875rem;flex-shrink:0;">+ 添加网站</button>
                    </div>
                </div>
            </div>

            <div style="flex:1;overflow-y:auto;padding:0.75rem;background:#f9fafb;">
                <div id="empty-state" style="display:flex;align-items:center;justify-content:center;height:100%;">
                    <p class="text-gray-500" style="font-size:0.8125rem;">请选择一个分类</p>
                </div>
                
                <div id="sites-table" style="display:none;background:white;border-radius:0.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.1);overflow:hidden;">
                    <table>
                        <thead>
                            <tr>
                                <th>标题</th>
                                <th>URL</th>
                                <th>权重</th>
                                <th>描述</th>
                                <th style="text-align:right;">操作</th>
                            </tr>
                        </thead>
                        <tbody id="sites"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 分类弹窗 -->
    <div id="category-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <h3 style="font-size:1.25rem;font-weight:600;margin-bottom:1rem;" id="category-modal-title">添加分类</h3>
            <form id="category-form" autocomplete="off">
                <input type="hidden" id="category-id" name="category-id">
                <div class="mb-3">
                    <label>分类名称</label>
                    <input type="text" id="category-name" name="category-name" required autocomplete="off">
                </div>
                <div class="mb-3">
                    <label>权重</label>
                    <input type="number" id="category-sort" name="category-sort" min="1" value="1" autocomplete="off">
                    <p class="text-xs text-gray-500" style="margin-top:0.25rem;">数字越大，显示越靠前</p>
                </div>
                <div class="mb-3">
                    <label style="display:flex;align-items:center;cursor:pointer;">
                        <input type="checkbox" id="category-password-enabled" name="category-password-enabled" style="margin:0;margin-right:0.5rem;width:16px;height:16px;cursor:pointer;">
                        <span style="font-weight:500;color:#374151;">启用密码保护</span>
                    </label>
                    <input type="password" id="category-password" name="category-password" placeholder="输入访问密码" autocomplete="new-password" style="margin-top:0.5rem;display:none;">
                </div>
          
                <div style="display:flex;justify-content:flex-end;gap:0.75rem;margin-top:1.25rem;">
                    <button type="button" onclick="closeCategoryModal()" class="btn bg-gray-200 text-gray-700">取消</button>
                    <button type="submit" class="btn btn-primary">保存</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 确认弹窗 -->
    <div id="confirm-modal" class="modal" style="display:none;">
        <div class="modal-content" style="max-width:24rem;">
            <h3 style="font-size:1.125rem;font-weight:600;margin-bottom:0.75rem;color:#1f2937;" id="confirm-title"></h3>
            <p style="color:#6b7280;font-size:0.875rem;margin-bottom:1.25rem;" id="confirm-message"></p>
            <div style="display:flex;justify-content:flex-end;gap:0.75rem;">
                <button type="button" onclick="closeConfirmModal(false)" class="btn bg-gray-200 text-gray-700">取消</button>
                <button type="button" onclick="closeConfirmModal(true)" class="btn" style="background:#ef4444;color:white;">确定</button>
            </div>
        </div>
    </div>

    <!-- 网站弹窗 -->
    <div id="site-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <h3 style="font-size:1.25rem;font-weight:600;margin-bottom:1rem;" id="site-modal-title">添加网站</h3>
            <form id="site-form" autocomplete="off">
                <input type="hidden" id="site-id" name="site-id">
                <div class="mb-3">
                    <label>网站标题</label>
                    <input type="text" id="site-title" name="site-title" required autocomplete="off">
                </div>
                <div class="mb-3">
                    <label>网站URL</label>
                    <input type="url" id="site-url" name="site-url" required autocomplete="off">
                </div>
                <div class="mb-3">
                    <label>权重</label>
                    <input type="number" id="site-sort" name="site-sort" min="1" value="1" autocomplete="off" style="appearance:textfield;-webkit-appearance:textfield;-moz-appearance:textfield;">
                    <style>
                        #site-sort::-webkit-outer-spin-button,
                        #site-sort::-webkit-inner-spin-button {
                            -webkit-appearance: none;
                            margin: 0;
                        }
                    </style>
                </div>
                <div class="mb-3">
                    <label>描述</label>
                    <textarea id="site-description" name="site-description" rows="2" maxlength="100" autocomplete="off"></textarea>
                </div>
                <div style="display:flex;justify-content:flex-end;gap:0.75rem;margin-top:1.25rem;">
                    <button type="button" onclick="closeSiteModal()" class="btn bg-gray-200 text-gray-700">取消</button>
                    <button type="submit" class="btn btn-primary" style="background:#10b981;">保存</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    let categories = [];
    let selectedCategoryId = null;
    let sites = [];

    async function loadCategories() {
        const res = await fetch('api.php?action=categories');
        categories = await res.json();
        renderCategories();
        if (categories.length > 0 && !selectedCategoryId) {
            selectCategory(categories[0].id);
        }
    }

    function renderCategories() {
        const html = categories.map(cat => `
            <div class="category-item ${selectedCategoryId == cat.id ? 'active' : ''}" onclick="selectCategory(${cat.id})">
                <div style="display:flex;align-items:center;gap:0.375rem;margin-bottom:0.25rem;">
                    <h3 style="font-weight:500;font-size:0.8125rem;color:${selectedCategoryId == cat.id ? '#1d4ed8' : '#111827'};flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${cat.name}</h3>
                    ${cat.is_password_enabled == 1 ? '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" style="flex-shrink:0;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>' : ''}
                </div>
                <p class="text-xs text-gray-500" style="font-size:0.6875rem;">权重: ${cat.sort_order}</p>
            </div>
        `).join('');
        document.getElementById('categories').innerHTML = html;
    }

    async function selectCategory(id) {
        selectedCategoryId = id;
        renderCategories();
        
        const category = categories.find(c => c.id == id);
        document.getElementById('category-title').textContent = category.name + ' - 网站管理';
        document.getElementById('header').style.display = 'block';
        document.getElementById('empty-state').style.display = 'none';
        
        await loadSites(id);
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
        sites = await res.json();
        
        if (sites.length === 0) {
            document.getElementById('sites-table').style.display = 'none';
            document.getElementById('empty-state').style.display = 'flex';
            document.getElementById('empty-state').innerHTML = '<div style="text-align:center;"><p class="text-gray-500" style="margin-bottom:1rem;">该分类下暂无网站</p><button onclick="showSiteModal()" class="btn btn-primary" style="background:#10b981;">添加第一个网站</button></div>';
            return;
        }
        
        document.getElementById('sites-table').style.display = 'block';
        document.getElementById('empty-state').style.display = 'none';
        
        const html = sites.map(site => {
            const firstChar = site.title.charAt(0).toUpperCase();
            const color = getRandomColor(hashCode(site.title + site.id));
            return `
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.375rem;">
                            <div class="icon-box" style="background:${color};">${firstChar}</div>
                            <span style="font-weight:500;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:0.8125rem;">${site.title}</span>
                        </div>
                    </td>
                    <td><a href="${site.url}" target="_blank" style="color:#3b82f6;text-decoration:none;display:block;max-width:18rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:0.8125rem;">${site.url}</a></td>
                    <td style="color:#6b7280;text-align:center;font-size:0.8125rem;">${site.sort_order}</td>
                    <td style="color:#6b7280;max-width:12rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:0.6875rem;">${site.description || ''}</td>
                    <td style="text-align:right;">
                        <div style="display:flex;justify-content:flex-end;gap:0.25rem;">
                            <button onclick="editSite(${site.id})" class="btn-icon btn-edit" title="编辑">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>
                            <button onclick="deleteSite(${site.id})" class="btn-icon btn-delete" title="删除">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
        document.getElementById('sites').innerHTML = html;
    }

    function showCategoryModal() {
        document.getElementById('category-modal-title').textContent = '添加分类';
        document.getElementById('category-form').reset();
        document.getElementById('category-id').value = '';
        document.getElementById('category-modal').style.display = 'flex';
    }

    function closeCategoryModal() {
        document.getElementById('category-modal').style.display = 'none';
    }

    function editCategory(id) {
        const cat = categories.find(c => c.id == id);
        document.getElementById('category-modal-title').textContent = '编辑分类';
        document.getElementById('category-id').value = cat.id;
        document.getElementById('category-name').value = cat.name;
        document.getElementById('category-sort').value = cat.sort_order;
        document.getElementById('category-password-enabled').checked = cat.is_password_enabled == 1;
        document.getElementById('category-password').value = cat.password || '';
        document.getElementById('category-password').style.display = cat.is_password_enabled == 1 ? 'block' : 'none';
        document.getElementById('category-modal').style.display = 'flex';
    }

    async function deleteCategory(id) {
        const category = categories.find(c => c.id == id);
        const confirmed = await showConfirmModal(`确定删除此分类？`, `分类"${category.name}"下的所有网站也会被删除。`);
        if (!confirmed) return;
        const formData = new FormData();
        formData.append('id', id);
        await fetch('api.php?action=category_delete', {method: 'POST', body: formData});
        if (selectedCategoryId == id) {
            selectedCategoryId = null;
            document.getElementById('header').style.display = 'none';
            document.getElementById('sites-table').style.display = 'none';
            document.getElementById('empty-state').style.display = 'flex';
            document.getElementById('empty-state').innerHTML = '<p class="text-gray-500">请选择一个分类</p>';
        }
        loadCategories();
    }

    function showSiteModal() {
        document.getElementById('site-modal-title').textContent = '添加网站';
        document.getElementById('site-form').reset();
        document.getElementById('site-id').value = '';
        // 延迟清空，确保浏览器自动填充后再清除
        setTimeout(() => {
            document.getElementById('site-title').value = '';
            document.getElementById('site-url').value = '';
            document.getElementById('site-sort').value = '1';
            document.getElementById('site-description').value = '';
        }, 10);
        document.getElementById('site-modal').style.display = 'flex';
    }

    function closeSiteModal() {
        document.getElementById('site-modal').style.display = 'none';
    }

    function editSite(id) {
        const site = sites.find(s => s.id == id);
        document.getElementById('site-modal-title').textContent = '编辑网站';
        document.getElementById('site-id').value = site.id;
        document.getElementById('site-title').value = site.title;
        document.getElementById('site-url').value = site.url;
        document.getElementById('site-sort').value = site.sort_order;
        document.getElementById('site-description').value = site.description || '';
        document.getElementById('site-modal').style.display = 'flex';
    }

    async function deleteSite(id) {
        const site = sites.find(s => s.id == id);
        const confirmed = await showConfirmModal('确定删除此网站？', `网站"${site.title}"将被永久删除。`);
        if (!confirmed) return;
        const formData = new FormData();
        formData.append('id', id);
        await fetch('api.php?action=site_delete', {method: 'POST', body: formData});
        loadSites(selectedCategoryId);
    }

    document.getElementById('category-password-enabled').addEventListener('change', function() {
        document.getElementById('category-password').style.display = this.checked ? 'block' : 'none';
    });

    document.getElementById('category-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('category-id').value;
        const data = {
            name: document.getElementById('category-name').value,
            sortOrder: parseInt(document.getElementById('category-sort').value) || 1,
            isPasswordEnabled: document.getElementById('category-password-enabled').checked ? 1 : 0,
            password: document.getElementById('category-password-enabled').checked ? document.getElementById('category-password').value : null
        };
        
        if (id) {
            data.id = id;
            await fetch('api.php?action=category_update', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            });
        } else {
            await fetch('api.php?action=categories', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            });
        }
        
        closeCategoryModal();
        loadCategories();
    });

    document.getElementById('site-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('site-id').value;
        const data = {
            title: document.getElementById('site-title').value,
            url: document.getElementById('site-url').value,
            description: document.getElementById('site-description').value,
            sortOrder: parseInt(document.getElementById('site-sort').value) || 1,
            categoryId: selectedCategoryId
        };
        
        if (id) {
            data.id = id;
            await fetch('api.php?action=site_update', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            });
        } else {
            await fetch('api.php?action=sites', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            });
        }
        
        closeSiteModal();
        loadSites(selectedCategoryId);
    });

    let confirmResolve = null;
    function showConfirmModal(title, message) {
        return new Promise((resolve) => {
            confirmResolve = resolve;
            document.getElementById('confirm-title').textContent = title;
            document.getElementById('confirm-message').textContent = message;
            document.getElementById('confirm-modal').style.display = 'flex';
        });
    }

    function closeConfirmModal(result) {
        document.getElementById('confirm-modal').style.display = 'none';
        if (confirmResolve) {
            confirmResolve(result);
            confirmResolve = null;
        }
    }

    loadCategories();
    </script>
</body>
</html>
