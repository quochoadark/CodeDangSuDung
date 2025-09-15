var headingElement = document.querySelector(".heading")

// Lấy ra nội dung text của element sử dụng innerText và textContent
// Inner trả về text nhưng nếu có lệnh tác động vào thì sẽ bị ảnh hưởng 
// textContent về text nhưng không bị tác động bởi bất cứ gì sẽ trả về nguyên mẫu 
console.log(headingElement.innerText)
console.log(headingElement.textContent)

// Gán nội dung 
headingElement.textContent = "new heading"

// InnerHTML, outerHTML 
// InnerHTMl thêm được tất cả mọi thứ vào (thường được dùng)
headingElement.innerHTML = "<h1>Heading</h1>"
// OuterHTML khi thêm sẽ thay thế toàn bộ cái cũ

// Style: giống với thằng style css  
var headingElement1 = document.querySelector(".heading1")
headingElement1.style.width = "100px";
headingElement1.style.height = "50px";
headingElement1.style.backgroundColor = "red";

// Có thể sử dụng Object assign để gán 
Object.assign(headingElement.style, {
    width: "100px",
    color: "green"
});

// ClassList 
// Các phương thức 
// add: thêm class vào element 
// contain: kiểm tra xem class có trong element này hay không (trả về true or false (nhớ thêm console.log))
// remove: xóa class 
// toggle: Nếu có class đó ở element mà gọi toggle thì nó xóa bỏ class đó, 
// còn nếu không có class đó mà gọi toggle thì nó thêm class đó
var boxElement = document.querySelector(".box")
// console.log(boxElement.classList.length) lấy ra số lượng class
// console.log(boxElement.classList.value) => box box-1 