function viewDetails(row) {
  const detailRow = row.nextElementSibling;
  if (detailRow && detailRow.classList.contains('detail-row')) {
    detailRow.classList.toggle('hidden');
  }
}
