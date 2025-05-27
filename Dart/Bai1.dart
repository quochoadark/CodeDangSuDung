void main() {
  // Xuất
  String name = "Tung";
  print("Hello $name");

  // Cách khai báo biến

  // Sử dụng var để dart tự suy luận kiểu
  var ten = "Tung";
  var tuoi = 18;
  // Khai báo với kiểu cụ thể
  String name1 = "Tung";
  // Khai báo với kiểu object
  Object tenNguoiDung = "Tung";

  // Cách gắn giá trị null (Phải thêm dấu chấm hỏi vì nếu ghi bình thường sẽ bị lỗi)
  String? ten2;
  ten2 = null;
  ten2 = "Tung";

  // Final chỉ được gán giá trị 1 lần nhưng không cần gán giá trị liền ngay lập tức
  //  final int age;   age = 1;
  // Const cũng chỉ được gán giá trị 1 lần nhưng cần gán giá trị liền ngay lập tức
  // const int age = 1;

  // Các phép toán: giống java (có thêm chia lấy phần nguyên ~/ )

  // Kiem tra co phai la String. Nếu kiểm tra không phải thì thêm ! vào sau is
  Object obj = "Hello";
  if (obj is String) {
    print("obj la mot String");
  }

  // Ep kieu: sử dụng as
  String str = obj as String;

  // ?? dùng để gán null
  String? ketQua;
  ketQua ??= "chua xet";
  print(ketQua);
}
