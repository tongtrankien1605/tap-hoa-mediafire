<?php
// Danh sách tên tiếng Việt không dấu
$firstNames = ["An", "Bao", "Cuong", "Dung", "Hanh", "Hung", "Linh", "Minh", "Ngoc", "Phuc", "Quan", "Son", "Thanh", "Thao", "Tu", "Vy"];
$lastNames = ["Nguyen", "Tran", "Le", "Pham", "Hoang", "Phan", "Vu", "Vo", "Dang", "Bui", "Do", "Ho", "Ngo", "Duong", "Ly"];

// Hàm tạo tên ngẫu nhiên
function generateRandomName($firstNames, $lastNames) {
    $firstName = $firstNames[array_rand($firstNames)];
    $lastName = $lastNames[array_rand($lastNames)];
    return [$firstName, $lastName];
}

// Hàm tạo email ngẫu nhiên
function generateRandomEmail() {
    $letters = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 6);
    $numbers = substr(str_shuffle("0123456789"), 0, 6);
    return $letters . $numbers . "@yopmail.com";
}

// Hàm tạo mật khẩu ngẫu nhiên
function generateRandomPassword() {
    $numbers = substr(str_shuffle("0123456789"), 0, 6);
    return "KienTT" . $numbers . "@";
}

// Tạo mã code ngẫu nhiên 6 ký tự
$randomCode = bin2hex(random_bytes(3));

// Kiểm tra phương thức yêu cầu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

    if ($quantity > 0) {
        $results = [];
        $emailFileContent = [];
        $firstNameFileContent = [];
        $lastNameFileContent = [];
        $passwordFileContent = [];
        $mediaFileContent = [];

        // Tạo thư mục "file" nếu chưa tồn tại
        $folder = "file";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        for ($i = 0; $i < $quantity; $i++) {
            [$firstName, $lastName] = generateRandomName($firstNames, $lastNames);
            $email = generateRandomEmail();
            $password = generateRandomPassword();
            $message = "Nhắn tin mã đơn hàng cho shop để nhận mật khẩu";
            
            $line = "$email|$password|$message|$firstName|$lastName";
            $mediaLine = "$email|$message|$firstName|$lastName";  
            $results[] = $line;
            $emailFileContent[] = $email;
            $firstNameFileContent[] = $firstName;
            $lastNameFileContent[] = $lastName;
            $passwordFileContent[] = $password;
            $mediaFileContent[] = $mediaLine;
        }

        // Lưu file vào thư mục "file"
        file_put_contents("$folder/{$randomCode}_emails.txt", implode("\n", $emailFileContent));
        file_put_contents("$folder/{$randomCode}_first_names.txt", implode("\n", $firstNameFileContent));
        file_put_contents("$folder/{$randomCode}_last_names.txt", implode("\n", $lastNameFileContent));
        file_put_contents("$folder/{$randomCode}_passwords.txt", implode("\n", $passwordFileContent));
        file_put_contents("$folder/{$randomCode}_all_results.txt", implode("\n", $results));
        file_put_contents("$folder/{$randomCode}_media.txt", implode("\n", $mediaFileContent));
        
        // Tạo file ZIP chứa tất cả các file
        $zipFile = "$folder/{$randomCode}_data.zip";
        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
            $zip->addFile("$folder/{$randomCode}_emails.txt", "emails.txt");
            $zip->addFile("$folder/{$randomCode}_first_names.txt", "first_names.txt");
            $zip->addFile("$folder/{$randomCode}_last_names.txt", "last_names.txt");
            $zip->addFile("$folder/{$randomCode}_passwords.txt", "passwords.txt");
            $zip->addFile("$folder/{$randomCode}_all_results.txt", "all_results.txt");
            $zip->addFile("$folder/{$randomCode}_media.txt", "media.txt");
            $zip->close();
        }

        // Hiển thị mã code và liên kết tải về
        echo "<h1>Kết quả xử lý</h1>";
        echo "<p>Mã code: <strong>$randomCode</strong></p>";
        echo "<p><a href='$folder/{$randomCode}_emails.txt' download>Download File Emails</a></p>";
        echo "<p><a href='$folder/{$randomCode}_first_names.txt' download>Download File First Names</a></p>";
        echo "<p><a href='$folder/{$randomCode}_last_names.txt' download>Download File Last Names</a></p>";
        echo "<p><a href='$folder/{$randomCode}_passwords.txt' download>Download File Passwords</a></p>";
        echo "<p><a href='$folder/{$randomCode}_all_results.txt' download>Download File Tổng</a></p>";
        echo "<p><a href='$folder/{$randomCode}_media.txt' download>Download File Media</a></p>";
        echo "<p><a href='$zipFile' download>Download All Files (ZIP)</a></p>";

        // Hiển thị kết quả trên màn hình
        echo "<pre>" . implode("\n", $results) . "</pre>";
    } else {
        echo "<p>Vui lòng nhập số lượng hợp lệ.</p>";
    }
} else {
    echo "<p>Phương thức không hợp lệ.</p>";
}
?>
