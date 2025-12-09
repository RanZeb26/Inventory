<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Mini Accounting UI</title>

<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        display: flex;
        height: 100vh;
        background: #f5f7fa;
    }

    /* Sidebar */
    .sidebar {
        width: 220px;
        background: #ffffff;
        border-right: 1px solid #ddd;
        padding: 20px 0;
    }

    .sidebar h3 {
        margin: 0 20px 15px;
        color: #333;
        font-size: 18px;
    }

    .nav-item {
        padding: 12px 20px;
        cursor: pointer;
        color: #333;
    }

    .nav-item.active,
    .nav-item:hover {
        background: #e8f0fe;
        color: #1a73e8;
    }

    /* Main Container */
    .main {
        flex: 1;
        display: flex;
        flex-direction: row;
    }

    /* List Area */
    .list-section {
        width: 40%;
        border-right: 1px solid #ddd;
        padding: 20px;
        overflow-y: auto;
    }

    .list-section h2 {
        margin-top: 0;
    }

    .invoice-item {
        padding: 15px;
        background: #fff;
        margin-bottom: 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
        cursor: pointer;
    }

    .invoice-item:hover {
        background: #f0f7ff;
        border-color: #1a73e8;
    }

    /* Preview Panel */
    .preview {
        flex: 1;
        padding: 30px;
        overflow-y: auto;
    }

    .invoice-preview {
        background: white;
        padding: 25px;
        border-radius: 12px;
        width: 350px;
        border: 1px solid #ddd;
    }

    .tag {
        background: #b9bfc8;
        color: white;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        display: inline-block;
        margin-bottom: 10px;
    }

</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h3>Sales</h3>
    <div class="nav-item active" onclick="loadMenu('Invoices')">Invoices</div>
    <div class="nav-item" onclick="loadMenu('Sales Receipts')">Sales Receipts</div>
    <div class="nav-item" onclick="loadMenu('Payments Received')">Payments Received</div>

    <h3 style="margin-top:20px;">Purchases</h3>
    <div class="nav-item" onclick="loadMenu('Bills')">Bills</div>
    <div class="nav-item" onclick="loadMenu('Vendors')">Vendors</div>
</div>

<!-- MAIN PAGE -->
<div class="main">

    <!-- LIST SECTION -->
    <div class="list-section">
        <h2 id="listTitle">Invoices</h2>
        <div id="listContainer"></div>
    </div>

    <!-- PREVIEW PANEL -->
    <div class="preview">
        <div id="previewContainer">
            <p>Select an item to preview</p>
        </div>
    </div>

</div>

<script>
// Fake data for demo
const data = {
    "Invoices": [
        { id: "INV-0001", date: "2025-01-10", customer: "ABC Corp", amount: 15000 },
        { id: "INV-0002", date: "2025-01-12", customer: "John Doe", amount: 8950 },
        { id: "INV-0003", date: "2025-01-14", customer: "Metro Supplies", amount: 32000 },
    ],
    "Bills": [
        { id: "BILL-9001", date: "2025-01-08", vendor: "Water Utility", amount: 2500 },
        { id: "BILL-9002", date: "2025-01-11", vendor: "Electric Co.", amount: 6800 },
    ],
    "Sales Receipts": [
        { id: "SR-2001", date: "2025-01-04", customer: "Walk-in", amount: 500 },
    ]
};

// Load selected menu
function loadMenu(menuName) {
    document.getElementById("listTitle").innerText = menuName;

    let list = data[menuName] || [];
    let html = "";

    list.forEach(item => {
        html += `
            <div class="invoice-item" onclick='loadPreview(${JSON.stringify(item)})'>
                <strong>${item.id}</strong><br>
                <small>${item.date}</small><br>
                <span>${item.customer || item.vendor}</span>
            </div>
        `;
    });

    document.getElementById("listContainer").innerHTML = html;
}

// Load preview panel
function loadPreview(item) {
    document.getElementById("previewContainer").innerHTML = `
        <div class="invoice-preview">
            <div class="tag">Draft</div>
            <h2>INVOICE</h2>
            <p><strong># ${item.id}</strong></p>
            <p><strong>Date:</strong> ${item.date}</p>
            <p><strong>Name:</strong> ${item.customer || item.vendor}</p>
            <p><strong>Amount Due:</strong> ₱${item.amount.toLocaleString()}</p>
        </div>
    `;
}

// Load default menu
loadMenu("Invoices");

</script>

</body>
</html>
