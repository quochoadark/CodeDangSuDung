// Datatype 
// Co 2 kieu la: let(su dung) va var 
// Let ung voi all kieu du lieu
let a = 5;
let b = 6;
let c = "Hòa";
console.log("a:", a, "b+c:", b + c);

// Dấu `sẽ in ra toàn bộ những thứ bên trong 
// Ngoai ra dau `co the xuong dong cau khi in ra 

// Typeof: dung de in ra bien do thuoc kieu du lieu nao

// Hang so: const 

// Toan tu cong trong js la cong tu trai sang phai 
console.log('a' + 5 + 4);  // => a54 
console.log(4 + 5 + "a");  // => 9a 

// Tim hieu them cac method cua String 
// Cach search google: js + câu hỏi (Tiếng anh) (sử dụng google dich ho tro) 

// Kieu so trong js: number (bao gom int float long double)
// Khi string 1 so (VD: let d = "5") thi js se co gang convert chuoi thanh so 
// Convert thanh so nguyen co the su dung Number o phia truoc hoac + o phia truoc (let d = Number(a)  or let d = +a)

// Chuyển số nguyên thành chuỗi: toString()
// Chuyển số thập phân thành chuỗi: toFixed()

// Template String: Khi nối chuỗi sẽ linh hoạt hơn không cần dấu cộng 
console.log(`Check String: a = ${a}  b = ${a + b}`);

// Kieu du lieu Object 
let obj = {
    name: "Hòa",
    address: "TQP",
    'Gender': "male"
};
console.log(typeof (obj));
console.log("My name is: ", obj.name);

// Kieu du lieu Array
let i = [];
let a1 = ["Mu", "Liver"];
console.log("Check array: ", typeof (a1), a1);
console.log("Check array: ", a1[0]);
// Object trong array
let family = [
    { name: "Hưng", age: 18 },
    { name: "Hùng", age: 19 },
    { name: "Ựdsd", age: 20 }
];
console.log("My family: ", family);


// So sanh == và === 
// Nen su dung === vi 3 dau bang se so sanh type va value con 2 dau bang chi so sanh value

// Len mang xem lại empty, null, undefined

// Vong lap voi Array 
let arr = ["Mu", "Liver", "Chel"];
for (let i = 0; i < arr.length; i++) {
    console.log("Check i: ", a[i]);
}
console.log("Check i: ", i);
// Pham vi cua bien var rong hon bien let. Nên sử dụng let
// While(true): vong lap vo han 
// Ôn lại continue và break 


// Hàm trong js 
function sum(a, b) {
    return a + b;
}
console.log("Sum a + b = ", sum(5, 10));

// Arrow function khac voi function o cho khai bao bien
let tong = (a, b) => {
    return a + b;
}
console.log("Arrow function: ", tong(5, 5));

// Method là những hàm nằm trong 1 class 

// 3 cai duoi hoc cho vui 
// Callback: goi ham khac 
// Settimeout: thời gian để in ra 1 cái gì đó
// Setinterval: In ra theo thời gian (VD: 5000 là cứ 5 giây ỉn ra 1 lần )
let thuong = (a, b, callback) => {
    let tong = a + b;
    setTimeout(() => {
        callback(tong);
    }, 5000)  // Tinh theo don vi mili giay => 5000 = 5s
    setInterval(() => {
        callback(tong);
    }, 5000)  // Tinh theo don vi mili giay => 5000 = 5s

    // Cách dừng setInterval lên mạng xem
}

let printthuong = (message) => {
    console.log("Check: ", message);
}

thuong(6, 9, printthuong)




