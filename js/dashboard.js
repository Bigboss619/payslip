document.addEventListener('DOMContentLoaded', async () => {
    await loadDashboardData();
});

async function loadDashboardData() {
    try {
        const response = await fetch('../includes/dashboard.php');
        const result = await response.json();
        
        console.log('✅ API Response:', result);
        
        if (result.success) {
            renderStats(result.stats);
            renderRecentPayslips(result.recent_payslips || []);
            updateWelcome(result.stats?.user_name);
        }
    } catch (error) {
        console.error('Dashboard error:', error);
        showFallback();
    }
}

function renderStats(stats) {
    document.querySelector('[data-stat="total_payslips"]').textContent = stats.total_payslips || 0;
    document.querySelector('[data-stat="last_salary"]').textContent = formatCurrency(stats.last_salary || 0);
    document.querySelector('[data-stat="current_month"]').textContent = stats.current_month || 'Current Month';
}

function renderRecentPayslips(payslips) {
    const tbody = document.getElementById('recent-tbody');
    const countEl = document.getElementById('payslips-count');
    
    if (payslips.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="py-12 text-center text-gray-500">No recent payslips found</td></tr>';
        countEl.textContent = '0 payslips';
        return;
    }
    
    tbody.innerHTML = payslips.map(pay => `
        <tr class="border-b hover:bg-gray-50">
            <td class="py-3 font-medium">${pay.month} ${pay.year}</td>
            <td class="py-3">${formatCurrency(pay.gross_salary)}</td>
            <td class="py-3 font-semibold text-green-600">${formatCurrency(pay.net_salary)}</td>
            <td>
                <span class="px-2 py-1 text-xs rounded-full 
                    ${pay.status === 'Paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                    ${pay.status || 'Paid'}
                </span>
            </td>
            <td class="py-3">
                <a href="payslip-view.php?id=${pay.id || pay.batch_id}" 
                   class="text-blue-600 hover:text-blue-800 hover:underline">
                    View →
                </a>
            </td>
        </tr>
    `).join('');
    
    countEl.textContent = `${payslips.length} payslips`;
}

function updateWelcome(userName) {
    document.getElementById('welcome-title').textContent = `Welcome, ${userName} 👋`;
    document.getElementById('welcome-user').textContent = `Welcome, ${userName}`;
}

function formatCurrency(amount) {
    return `₦${parseFloat(amount || 0).toLocaleString('en-NG')}`;
}

function showFallback() {
    document.querySelectorAll('[data-stat]').forEach(el => el.textContent = '—');
    document.getElementById('recent-tbody').innerHTML = '<tr><td colspan="5" class="py-12 text-center text-gray-500">Unable to load data</td></tr>';
}document.addEventListener('DOMContentLoaded', async () => {
    try {
        await loadDashboardData();
    } catch (error) {
        console.error('Dashboard init error:', error);
    }
});

async function loadDashboardData() {
    try {
        const response = await fetch('../includes/dashboard.php');
        const result = await response.json();
        
        console.log('✅ API Response:', result);
        
        if (result.success) {
            renderStats(result.stats || {});
            renderRecentPayslips(result.recent_payslips || []);
        } else {
            console.error('API Error:', result.error);
            showFallback();
        }
    } catch (error) {
        console.error('Fetch error:', error);
        showFallback();
    }
}

function renderStats(stats) {
    // Safe element checks
    const totalEl = document.querySelector('[data-stat="total_payslips"]');
    if (totalEl) totalEl.textContent = stats.total_payslips || 0;
    
    const lastEl = document.querySelector('[data-stat="last_salary"]');
    if (lastEl) lastEl.textContent = formatCurrency(stats.last_salary || 0);
    
    const monthEl = document.querySelector('[data-stat="current_month"]');
    if (monthEl) monthEl.textContent = stats.current_month || 'Current Month';
    
    const welcomeEl = document.querySelector('main h1');
    if (welcomeEl) welcomeEl.textContent = `Welcome, ${stats.user_name || 'User'} 👋`;
}

function renderRecentPayslips(payslips = []) {
    const tbody = document.getElementById('recent-tbody');
    if (!tbody) {
        console.warn('❌ #recent-tbody not found!');
        return;
    }
    
    console.log('📋 Payslips count:', payslips.length);
    
    if (payslips.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="py-12 text-center text-gray-500">No recent payslips</td></tr>';
        return;
    }
    
    tbody.innerHTML = payslips.map(pay => `
        <tr class="border-b hover:bg-gray-50">
            <td class="py-3 font-medium">${pay.month || '?'} ${pay.year || ''}</td>
            <td class="py-3">${formatCurrency(pay.gross_salary)}</td>
            <td class="py-3 font-semibold text-green-600">${formatCurrency(pay.net_salary)}</td>
            <td class="py-3">
                <a href="payslip-view.php?id=${pay.id || pay.batch_id || 1}" 
                   class="text-blue-600 hover:underline font-medium">
                    View →
                </a>
            </td>
        </tr>
    `).join('');
}

function formatCurrency(amount) {
    return `₦${parseFloat(amount || 0).toLocaleString('en-NG')}`;
}

function showFallback() {
    // Safe fallback - check elements exist
    const statsEls = document.querySelectorAll('[data-stat]');
    statsEls.forEach(el => el.textContent = '—');
    
    const tbody = document.getElementById('recent-tbody');
    if (tbody) {
        tbody.innerHTML = '<tr><td colspan="4" class="py-12 text-center text-gray-500">Unable to load data</td></tr>';
    }
}