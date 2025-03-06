<!DOCTYPE html>
<html>
<head>
    <title>Browser Check and Redirect</title>
    <script type="text/javascript">
        // ตรวจสอบว่าเบราว์เซอร์เป็น LINE In-App Browser หรือไม่
        function isLineInAppBrowser() {
            const userAgent = navigator.userAgent.toLowerCase();
            return userAgent.indexOf('line') !== -1;
        }

        // ตรวจสอบว่าใช้ iOS หรือ Android
        function checkAndRedirect() {
            let userAgent = navigator.userAgent || navigator.vendor || window.opera;

            // ถ้าเป็น LINE In-App Browser ให้แสดงคำแนะนำให้เปิดใน Google Chrome
            if (isLineInAppBrowser()) {
                alert("Please open this page in Google Chrome for better experience.");

                // เปิด URL ใน Google Chrome ผ่านการเปิดลิงก์ในหน้าต่างใหม่
                const currentUrl = window.location.href;
                const chromeUrl = 'googlechrome://' + currentUrl;
                window.location.href = chromeUrl;
                return; // หยุดการทำงานเพื่อไม่ให้ไปยังส่วนอื่น
            }

            // ถ้าใช้ iOS, เช็คว่าเปิดใน Safari หรือไม่ และให้รีไดเร็คไปที่ Chrome
            if (/iPhone|iPad|iPod/.test(userAgent)) {
                if (!/CriOS/.test(userAgent)) {
                    window.location.href = "https://apps.apple.com/us/app/google-chrome/id535886823"; // ลิงก์ดาวน์โหลด Chrome
                }
            } else if (/Android/.test(userAgent)) {
                // ถ้าใช้ Android, เช็คว่าเปิดใน Chrome หรือไม่ และให้รีไดเร็คไปที่ Chrome
                if (!/Chrome/.test(userAgent)) {
                    window.location.href = "https://play.google.com/store/apps/details?id=com.android.chrome"; // ลิงก์ดาวน์โหลด Chrome
                }
            }
        }

        // เรียกใช้งานฟังก์ชันเมื่อโหลดหน้า
        window.onload = function () {
            checkAndRedirect();

            // หลังจากที่ตรวจสอบแล้วให้รีไดเร็คไปที่ sac_lotto
            setTimeout(function () {
                window.location.href = "sac_lotto"; // หลังจาก 2 วินาที รีไดเร็คไปยัง sac_lotto
            }, 2000); // รอ 2 วินาที (เพื่อให้ผู้ใช้มีเวลาในการตัดสินใจ)
        };
    </script>
</head>
<body>
<h1>โปรดรอการเปลี่ยนหน้า...</h1>
<p>หากไม่ใช่ Google Chrome, กรุณาติดตั้งหรือเปิดเว็บใน Google Chrome</p>
</body>
</html>

<?php
/* PHP block */
// ตรวจสอบให้แน่ใจว่าไม่มีการรีไดเร็คซ้ำในกรณีที่ผู้ใช้ทำการเปลี่ยนเบราว์เซอร์แล้ว
header('Location: sac_lotto');
exit;
?>
