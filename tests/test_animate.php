<!--!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ขยับภาพ PNG</title>
    <style>
        #movingImage {
            position: absolute;
            left: 0;
            transition: transform 1s ease-in-out;
        }
    </style>
</head>
<body>
<img id="movingImage" src="../img/man.png" width="100" alt="Moving Image">

<script>
    let position = 0;
    let direction = 1;

    setInterval(() => {
        position += 50 * direction; // เลื่อนไป 50px
        if (position >= 300 || position <= 0) {
            direction *= -1; // เปลี่ยนทิศทางเมื่อถึงขอบ
        }
        document.getElementById("movingImage").style.transform = `translateX(${position}px)`;
    }, 3000);
</script>
</body>
</html-->

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ขยับภาพขึ้น-ลง</title>
    <style>
        #movingImage {
            position: absolute;
            top: 0;
            transition: transform 1s ease-in-out;
        }
    </style>
</head>
<body>
<img id="movingImage" src="../img/man.png" width="100" alt="Moving Image">

<script>
    let position = 100;
    let direction = 1;

    setInterval(() => {
        position += 50 * direction; // เลื่อนขึ้น-ลง 50px
        if (position >= 100 || position <= 0) {
            direction *= -1; // เปลี่ยนทิศทางเมื่อถึงขอบ
        }
        document.getElementById("movingImage").style.transform = `translateY(${position}px)`;
    }, 2000);
</script>
</body>
</html>

