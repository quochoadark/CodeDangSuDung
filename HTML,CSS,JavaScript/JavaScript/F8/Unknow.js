/*  Một số hàm built-in 
    + alert: Hiển thị thông báo
    + confirm: Cũng giống như alert nhưng có thêm nút ok và cancel
    + prompt: Giống 2 cái trên nhưng có thêm chỗ nhập thông tin
*/

/* Một vài hàm làm việc với String */
let myString = "Hoc JS tai F8";

// Hàm length 
console.log(myString.length);

// Find index
// Tìm xem chữ Js có vị trí là bao nhiêu    
// Indexof(Chỗ cần tìm)    => Trả về vị trí (number)
// Indexof(Chỗ cần tìm, vị trí tìm)
console.log(myString.indexOf("JS"));

// Cat chuoi
// Slice (start,end)
// Slice (start)
console.log(myString.slice(4, 6))

// Thay the
// replace(chỗ cũ, chõ mới)
console.log(myString.replace("JS", "Javascript"));
// Thay thế toàn bộ phần tử trùng (VD: trùng JS) thêm /.../g
// console.log(myString.replace(/JS/g,"Javascript"));

// Chuyen thanh upper case va lower case
console.log(myString.toLowerCase());
console.log(myString.toUpperCase());

// Trim: khoảng trắng ở 2 đầu
console.log(myString.trim());

let languages = "H , N , K"
// Split: Tách chuỗi thành array
console.log(languages.split(","))

// Lấy ký tự từ vị trí 
console.log(myString.charAt(2));


// Doi tuong Date
let date = new Date();
let year = date.getFullYear();  //  year
let month = date.getMonth() + 1; // month
let day = date.getDate(); //
let Hour = date.getHours();
// Đối với phút giây cũng vậy
console.log(day);


// Bổ sung vòng lặp 
// for/in lặp qua key(vi tri) của đối tượng (array,object,string)
// Với Object
let myInfo = {
    name: "Hoa",
    age: 18,
    address: "HCM, Vn"
}
for (let key in myInfo) {
    console.log(key);
}
for (let key in myInfo) {
    console.log(myInfo[key]);
}
// Với Array
let lg1 = ["dd", "hh", "mm"];
for (let key in lg1) {
    console.log(key)
}
for (let key in lg1) {
    console.log(lg1[key])
}
// Với String
// Lấy ra từng ký tự của 
let myString2 = "Javascript"
for (let key in myString2) {
    console.log(myString2[key])
}
// for/of lặp qua value(nội dung) của đối tượng (array hoặc string và ko dùng được cho object)
for (let key of myString2) {
    console.log(key)
}
for (let key of lg1) {
    console.log(key)
}

