// HTML Dom 
// 1. Element: ID, class, tag, CSS seletor, HTML collection 
// 2. Attribute
// 3. Text 

var headingNode1 = document.getElementById("heading")
console.log(headingNode1)
var headingNode2 = document.getElementsByClassName("heading1")
console.log(headingNode2)
var headingNode3 = document.getElementsByTagName("p")
console.log(headingNode3)
var headingNode5 = document.querySelector(".box .heading-2")
console.log(headingNode5)
// query selector all thì trả về tất cả cần không có all thì trả về 1

// Cách lấy khác: lấy element của 1 div 
var boxNode = document.querySelector(".box-1")
console.log(boxNode.querySelectorAll("li"))
// Có thể dùng với những cái khác
