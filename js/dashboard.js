// Your existing JS + Multi-HR magic!
document.addEventListener('DOMContentLoaded', async () => {
    try {
        await loadDashboardData();
    } catch (error) {
        console.error('Dashboard init error:', error);
    }
});

async function loadDashboardData() {
    try {
        // 🔥 Updated API endpoint
        const response = await fetch('../api/services/dashboard.php');
        const result = await response.json();
        
        // console.log('✅ Multi-HR Response:', result);
        
        if (result.success) {
            renderStats(result.stats || {});
            renderRecentPayslips(result.payslips || []);  // 🔥 Changed from recent_payslips
            updateWelcome(result.user);  // 🔥 New user object
            toggleHRButtons(result.user.role);  // 🔥 HR buttons
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
    // Your existing code + new stats
    document.querySelector('[data-stat="total_payslips"]').textContent = stats.total_payslips || 0;
    document.querySelector('[data-stat="last_salary"]').textContent = formatCurrency(stats.last_salary || 0);
    document.querySelector('[data-stat="current_month"]').textContent = stats.current_month || 'Current Month';
    
    // 🔥 NEW: HR-specific stats
    if (stats.total_employees) {
        const empEl = document.querySelector('[data-stat="total_employees"]');
        if (empEl) empEl.textContent = stats.total_employees;
    }
}

function renderRecentPayslips(payslips = []) {  // 🔥 Renamed param
    const tbody = document.getElementById('recent-tbody');
    if (!tbody) return console.warn('❌ #recent-tbody missing!');
    
    // console.log('📋 Loading', payslips.length, 'payslips');
    
    if (payslips.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="py-12 text-center text-gray-500">No payslips yet</td></tr>';
        return;
    }
    
    tbody.innerHTML = payslips.map(pay => `
        <tr class="border-b hover:bg-gray-50">
            <td class="py-3 font-medium">${pay.month || '?'} ${pay.year || ''}</td>
            <td class="py-3">${formatCurrency(pay.gross_salary)}</td>
            <td class="py-3 font-semibold text-green-600">${formatCurrency(pay.net_salary)}</td>
            <td class="py-3">
                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                    ${pay.status || (pay.employees ? `${pay.employees} staff` : 'Paid')}
                </span>
            </td>
            <td class="py-3">
                <a href="payslip.php?id=${pay.id || pay.batch_id}" 
                   class="text-blue-600 hover:underline">
                    View →
                </a>
            </td>
        </tr>
    `).join('');
}

function updateWelcome(user) {  // 🔥 New user object
    const welcomeEl = document.querySelector('h1');
    if (welcomeEl) {
        welcomeEl.innerHTML = `Welcome, <strong>${user.name}</strong> 
            ${user.hr_type ? `<span class="text-sm bg-${user.hr_type === 'MAIN' ? 'blue' : 'green'}-100 px-2 py-1 rounded-full">${user.hr_type}</span>` : ''} 👋`;
    }
}

function toggleHRButtons(role) {
    // 🔥 Show/hide HR upload button
    const uploadBtn = document.querySelector('a[href="upload.php"]');
    if (uploadBtn) {
        uploadBtn.style.display = role === 'HR' ? 'inline-flex' : 'none';
    }
}

function formatCurrency(amount) {
    return `₦${parseFloat(amount || 0).toLocaleString('en-NG')}`;
}

function showFallback() {
    document.querySelectorAll('[data-stat]').forEach(el => el.textContent = '—');
    const tbody = document.getElementById('recent-tbody');
    if (tbody) tbody.innerHTML = '<tr><td colspan="4" class="py-12 text-center text-gray-500">Unable to load</td></tr>';
}