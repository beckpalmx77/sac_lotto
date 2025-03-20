<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แสดงยอดขาย</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .sale-container {
            margin-top: 20px;
            text-align: center;
        }
        .sale-box {
            display: inline-block;
            padding: 15px;
            border: 2px solid #ccc;
            border-radius: 10px;
            margin: 10px;
            width: 250px;
            text-align: center;
        }
        .total {
            background: yellow;
            font-weight: bold;
            padding: 5px;
        }
        .sales-data {
            display: flex;
            justify-content: center;
            gap: 5px;
        }
        .sales-data div {
            width: 70px;
            padding: 5px;
            border: 1px solid #000;
        }
    </style>
</head>
<body class="container">

<h2 class="text-center mt-4">แสดงยอดขายตามชื่อ Sale</h2>

<div class="text-center">
    <input type="text" id="searchSale" class="form-control w-50 d-inline-block" placeholder="ป้อนชื่อ Sale">
    <button id="searchBtn" class="btn btn-primary">ค้นหา</button>
</div>

<div id="saleResult" class="sale-container"></div>

<script>
    let salesData = {
        "sale_001": { "name": "กทม.3", "total": 2000, "LL": 1000, "LE": 900, "AT": 100 },
        "sale_002": { "name": "กลาง 3", "total": 2500, "LL": 900, "LE": 900, "AT": 700 },
        "sale_003": { "name": "ตะวันตก", "total": 500, "LL": 300, "LE": 200, "AT": 0 }
    };

    $("#searchBtn").click(function () {
        let saleInput = $("#searchSale").val().trim();
        let saleKey = Object.keys(salesData).find(key => salesData[key].name.includes(saleInput));

        if (saleKey) {
            let sale = salesData[saleKey];
            let html = `
                    <div class="sale-box">
                        <div class="total">${sale.total}</div>
                        <div class="sales-data">
                            <div>LL: ${sale.LL}</div>
                            <div>LE: ${sale.LE}</div>
                            <div>AT: ${sale.AT}</div>
                        </div>
                        <h5>${sale.name}</h5>
                    </div>
                `;
            $("#saleResult").html(html);
        } else {
            $("#saleResult").html("<p class='text-danger'>ไม่พบข้อมูล</p>");
        }
    });
</script>

</body>
</html>

