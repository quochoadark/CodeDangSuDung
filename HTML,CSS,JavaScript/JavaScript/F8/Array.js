// // Một vài cái làm việc với Array
// var lg = ["JS","PHP","Ruby"];
// // toString: chuyển kiểu dữ liệu thành String
// console.log(lg.toString());
// // Join: Biến từ Array thành chuỗi
// console.log(lg.join("-"));
// // pop: xóa phần tử ở cuối mảng và trả về phần tử đã xóa
// console.log(lg.pop());
// // push: thêm 1 hoặc nhiều phàn tử vào cuối mảng 
// console.log(lg.push("Dart"));
// console.log(lg);
// // Shift: xóa đi 1 phần tử ở đầu mảng và trả về phần tử đã xóa
// // Unshift: thêm 1 hoặc nhiều phần tử ở đầu mảng
// // splice: xóa, cắt, chèn phần tử mới vào mảng   
// // splice (vitri, xoa 1 hoac nhieu phan tu tuong ung vi tri , chèn 1 hoac nhieu phần tử tương ứng vị trí)
// lg.splice(1)   
// // concat: Nối phần tử 
// // concat: a1.concat(a2)
// // slice: cắt mảng 
// // slice(vitri bat dau, vi ket thuc)    |   slice(1)  cắt hết  


/* Array methods:*/
let courses = [
    {
        id: 1,
        name: "Javascript",
        coin: 250
    },
    {
        id: 2,
        name: "HTML, CSS",
        coin: 0
    },
    {
        id: 3,
        name: "Ruby",
        coin: 0
    },
    {
        id: 4,
        name: "Ruby",
        coin: 400   
    },
    {
        id: 5,
        name: "ReactJS",
        coin: 500
    },
]
// For each: dùng để duyệt qua từng phần tử của mảng
courses.forEach(function(course,index){
    console.log(index,course)
});

// Every (trả về kiểu boolean): kiem tra tat ca cac phan tu thuoc 1 mang phai thoa dieu kien gi do
let isFree = courses.every(function(course,index){
    return course.coin === 0;
});
console.log(isFree);

// Some (trả về kiểu boolean): kiem tra 1 phan tu nao do thuoc 1 mang phai thoa dieu kien gi do
let isFree1 = courses.some(function(course,index){
    return course.coin === 0;
});
console.log(isFree1);

// Find: Tìm cái gì đó trong array chỉ trả về 1 phàn tử, nếu có sẽ trả về value của nó còn ko thì sẽ trả về null
let isFree2 = courses.find(function(course,index){
    return course.name === "Ruby";
});
console.log(isFree2);

// Filter: Cũng giống với Find nhưng có thể trả về nhiều phần tử
let isFree3 = courses.filter(function(course,index){
    return course.name === "Ruby";
});
console.log(isFree3);

// Map: Chỉnh sữa, thay đổi phần tử của 1 mảng
let newCourses = courses.map(function(course, index){
    // Muốn sữa dc bên trong thì phải có return 
    return{
        id: course.id,   // giu nguyen  
        name: `Khóa học: ${course.name}`,
        coin: course.coin,  // giu nguyen
        coinText: `Gia: ${course.coin}`,
        index: index
    }
});
console.log(newCourses)

// Reduce: Khi chúng ta muốn nhận về 1 giá trị duy nhất khi ta xử lý trên các phần tử trong cùng 1 array
// VD: Tổng số coin của courses
// Có thể sử dụng vòng lặp
let totalcoin = 0;
for(let course of courses){
    totalcoin += course.coin
}
console.log(totalcoin);
// Làm bằng reduce
// accumulator là biến lưu trữ, currentvalue là biến hiện tại 
// , currentIndex chỉ tới thằng currentValue, originArray là array nào gọi tới method reduce
let i = 0
function coinHandler(accumulator,currentValue, currentIndex,originArray){   
    i++;
    return accumulator + currentValue.coin;
}
let totalcoin1 = courses.reduce(coinHandler,0);  // Số 0 là khởi tạo biến lưu trữ
console.log(totalcoin1);

// Hàm includes: kiem tra co ky tu hay khong
let title = "Responsive web design"
// title (chu can tim, vi tri bat dau, vi tri ket thuc)
console.log(title.includes("web"),1)

// Hàm sort
const arr1 = [2,1,5,7,0];
arr1.sort();
console.log(arr1);






