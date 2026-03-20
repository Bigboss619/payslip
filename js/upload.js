
  const fileInput = document.getElementById("fileInput");
  const fileName = document.getElementById("fileName");
  const previewSection = document.getElementById("previewSection");
  const previewTable = document.getElementById("previewTable");

  function handleUpload() {
    const file = fileInput.files[0];

    if (!file) {
      alert("Please select a file");
      return;
    }

    fileName.innerText = "Selected file: " + file.name;

    // Simulated preview data (replace with backend later)
    previewSection.classList.remove("hidden");

    previewTable.innerHTML = `
      <tr>
        <td class="p-2 border">John Doe</td>
        <td class="p-2 border">IT</td>
        <td class="p-2 border">₦300,000</td>
        <td class="p-2 border">22</td>
        <td class="p-2 border">₦120,000</td>
        <td class="p-2 border">₦75,000</td>
        <td class="p-2 border">₦60,000</td>
        <td class="p-2 border">₦250,000</td>
      </tr>
    `;
  }
