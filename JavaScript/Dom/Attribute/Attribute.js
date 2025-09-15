// Dom Attribute 
// Gán attribute cho element 

var headingElement = document.querySelector("h1")

headingElement.title = "heading"
// Có thể sử dụng với các thẻ khác như: p,a,... 


// Cách add khác 
var headingElement1 = document.querySelector("h2")
headingElement1.setAttribute("class", "heading1")   // thuộc tính và tên 
// Có thể đặt bất cứ tên thuộc tính là gì kể cả id, href hoặc tên bất kỳ

// Cách xem tên của attribute đó của nó
console.log(headingElement1.getAttribute("class"))  // => heading1 
// Get attribute có thể lấy ra tên của thuộc tính mà mình tự tạo hoặc là của chính js tạo

headingElement1.title = "test"
console.log(headingElement1.title)  // Có thể lấy ra tên luôn nhưng chỉ với các thuộc tính của js còn tự tạo không lấy được


