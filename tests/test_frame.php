<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>อัพโหลดและแสดงตัวอย่างรูปภาพ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <!-- สร้างชุดอัพโหลด 8 ชุด -->
            <script>
                for (let i = 1; i <= 8; i++) {
    document.write(`
                        <div class="col-md-12 mb-3">
                            <label class="form-label">อัพโหลดรูปป้ายไวนิล ${i}</label>
                            <input type="file" name="lotto_file${i}[]" id="lotto_file${i}_input" class="form-control" multiple>
                        </div>
                        <div class="col-md-12">
                            <div class="row" id="lotto_file${i}_images"></div>
                        </div>
                    `);
}
            </script>
        </div>
    </div>

    <script>
$(document).ready(function() {
    for (let i = 1; i <= 8; i++) {
        $(`#lotto_file${i}_input`).on("change", function(event) {
            let files = event.target.files;
                    let previewContainer = $(`#lotto_file${i}_images`);
                    previewContainer.empty();

                    for (let j = 0; j < files.length; j++) {
                let reader = new FileReader();
                        reader.onload = function(e) {
                            let imgId = `preview_${i}_${j}`;
                            previewContainer.append(`
                                <div class="col-md-6 mb-3 text-center" id="${imgId}_container">
                                    <img src="${e.target.result}" class="img-fluid rounded border" style="max-width: 100%; height: auto;">
                                    <button class="btn btn-danger btn-sm mt-2" onclick="removeImage('${imgId}')">ลบ</button>
                                </div>
                            `);
                        };
                        reader.readAsDataURL(files[j]);
                    }
                });
    }
        });

        function removeImage(imgId) {
            $(`#${imgId}_container`).remove();
        }
    </script>
</body>
</html>

