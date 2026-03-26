// ✅ Complete Dashboard Logic
document.addEventListener('DOMContentLoaded', async () => {
    await loadDashboardData();
    setupEventListeners();
});

async function loadDashboardData() {
    try {
        const response = await fetch('../includes/dashboard.php');
        const result = await response.json();
        
        if (result.success) {
            renderStats(result.stats, result.user_name);
            renderRecentPayslips(result.recent_payslips);
        } else {
            console.error('Dashboard load error:', result.error);
        }
    } catch (error) {
        console.error('Dashboard fetch error:', error);
    }
}

function renderStats(stats, userName) {
    // Update welcome
    document.querySelector('h1').textContent = `Welcome, ${userName} 👋`;
    
    // Update stats cards
    if (stats.total_payslips !== undefined) {
        document.querySelectorAll('.grid [data-stat="total_payslips"]')?.forEach(el => el.textContent = stats.total_payslips);
    }
    if (stats.last_salary !== undefined) {
        document.querySelectorAll('.grid [data-stat="last_salary"]')?.forEach(el => el.textContent = formatCurrency(stats.last_salary));
    }
    if (stats.current_month !== undefined) {
        document.querySelectorAll('.grid [data-stat="current_month"]')?.forEach(el => el.textContent = stats.current_month);
    }
}

function renderRecentPayslips(payslips) {
    const tbody = document.querySelector('table tbody');
    if (!tbody || payslips.length === 0) return;
    
    tbody.innerHTML = payslips.map(pay => `
        <tr class="border-b hover:bg-gray-50">
            <td class="py-3 font-medium">${pay.month} ${pay.year}</td>
            <td class="py-3">${formatCurrency(pay.gross_salary)}</td>
            <td class="py-3 font-semibold text-green-600">${formatCurrency(pay.net_salary)}</td>
            <td class="py-3">
                <a href="payslip-view.php?id=${pay.id || pay.batch_id}" 
                   class="text-blue-600 hover:underline font-medium">
                    View
                </a>
            </td>
        </tr>
    `).join('');
}

function formatCurrency(amount) {
    return `₦${parseFloat(amount || 0).toLocaleString('en-NG')}`;
}

function setupEventListeners() {
    // Quick actions
    document.querySelector('.bg-blue-600')?.addEventListener('click', () => {
        window.location.href = 'payslip.php';
    });
    
    document.querySelector('.bg-green-600')?.addEventListener('click', () => {
        window.location.href = 'payslip-view.php?id=1'; // Last payslip
    });
}