var course = [
    {
        id: 1,
        name: "Js",
        coin: 0
    },
    {
        id: 2,
        name: "HTml",
        coin: 0
    },
    {
        id: 3,
        name: "CSS",
        coin: 0
    },
    {
        id: 4,
        name: "Ruby",
        coin: 0
    },
    {
        id: 5,
        name: "Java",
        coin: 0
    }
]

// For in 

// Với object 

// const person = {
//   name: "Alice",
//   age: 30,
//   city: "New York"
// };
// for (const key in person) {
//   console.log(`Key: ${key}, Value: ${person[key]}`);
// }
// Output:
// Key: name, Value: Alice
// Key: age, Value: 30
// Key: city, Value: New York

// Với array

// const fruits = ["apple", "banana", "cherry"];
// for (const index in fruits) {
//     console.log(`Index (Key): ${index}, Value: ${fruits[index]}`);
// }
// Output:
// Index (Key): 0, Value: apple
// Index (Key): 1, Value: banana
// Index (Key): 2, Value: cherry

// For of 

// Với array 
// const fruits = ["apple", "banana", "cherry"];
// for (const fruit of fruits) {
//   console.log(`Value: ${fruit}`);
// }
// Output:
// Value: apple
// Value: banana
// Value: cherry

// Với chuỗi
// const greeting = "Hello";

// for (const char of greeting) {
//   console.log(`Character: ${char}`);
// }
// Output:
// Character: H
// Character: e
// Character: l
// Character: l
// Character: o

// For each: duyệt qua từng phần tử 
// Các biến truyền vào của for each giống với map
var course = course.forEach(function (course, index) {
    console.log(course)
})
// Every: tất cả đều phải thỏa mãn điều kiện mới trả về true, chỉ cần 1 điều kiện sai trả về false
var course = course.every(function (course, index) {
    return course.coin === 0;
})
// some: Chỉ cần 1 điều kiện thỏa mãn mới trả về true, nếu không có cái nào thì trả về false 
var course = course.some(function (course, index) {
    return course.coin === 0;
})
// find: tìm kiếm và trả về đối tượng đó (chỉ trả về 1 đối tượng)
var course = course.find(function (course, index) {
    return course.name === "Ruby";
})
// filter: tìm kiếm và tất cả đối tượng thỏa điều kiện
var course = course.filter(function (course, index) {
    return course.name === "Ruby";
})
// Map: điều chỉnh những đối tượng trong mảng đó (thêm, xóa, sửa)
// Map(element,index,array)  // Nếu truyền thêm số thì nó sẽ bị undifined
function courseHandler() {
    return {
        id: course.id,
        name: `Khoa hoc: ${course.name}`,
        coinText: `Gia: ${course.coin}`
    } // Nó sẽ in ra toàn bộ mảng (bỏ phần coin và thêm phần coinText)
};
var newCourses = course.map(courseHandler)
console.log(newCourses)
// Reduce: lên mạng xem

// inlcudes: Kiem tra xem trong chuoi co ky tu do khong 
// Trả về true / false 
var title = "Responsive web design"
console.log(title.includes("web design", 2)) // So 2 la vi tri bat dau tim kiem 
