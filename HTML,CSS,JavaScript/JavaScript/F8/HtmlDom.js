// document.write sẽ viết thẳng vào trong body
// document.write("hello")

// Element: Gán element
// Với Id
// let headingNode = document.getElementById("Heading");
// console.log(headingNode);
// // Với class 
// let headingNode1 = document.getElementsByClassName("Headings");
// console.log(headingNode1);
// // Với tag name 
// let headingNode2 = document.getElementsByTagName("h3");
// console.log(headingNode2);
// // Với selector
// let headingNode3 = document.querySelectorAll(".selector");
// console.log(headingNode3);

// let box = document.querySelector(".box-1"); 
// console.log(box.querySelectorAll("li"));             
// console.log(box.querySelectorAll("p"));

// Attribute và lấy tên của attribute đó
// let attribute = document.querySelector('h1');
// // attribute.className = "heading";
// let attribute1= document.querySelector('a');
// attribute1.href = "A";
// // Nếu muốn đặt bất kỳ tên nào vào element thì phải làm bằng phương thức 
// // attribute.setAttribute("class","Heading")   // (tên attribute, tên nội dung muốn đặt)
// // Lấy ra giá trị 
// console.log(attribute.getAttribute("class"));

// Text: lấy ra nội dung của 2 cái trên
// let text = document.querySelector("h1");  // hoặc .Heading-text cũng được
// console.log(text.textContent);   // hoặc innerText 
// // Sửa đổi nội dung của nó
// text.innerText = "New Heading"
// InnerText: Nó sẽ không in ra nguyên bản nếu có 1 thuộc tính nào đó tác động (VD: display: none) 
// textContent: Nó sẽ mặc kệ tất cả thuộc tính và in ra tất cả

// // Thêm element vào trong element 
// // + InnerHTMl: thêm được tất cả các thẻ html css vào và cả nội dung 
// let boxElement = document.querySelector(".box"); 
// // Thêm vào các thẻ 
// boxElement.innerHTML = "<h6>Heading</h6>";  // Nếu bỏ các thẻ html đi thì chỉ còn là textnote thôi 
// // In ra Code html (css), content bên trong thẻ div 
// console.log(boxElement.innerHTML)
// // + OuterHTMl: lấy ra code html (css), content bên trong lẫn ngoài thẻ div
// console.log(boxElement.outerHTML)
// // Nếu sử dụng outer đề gán thì nó sẽ thay thế toàn bộ bằng code mới. Code cũ sẽ mất hết

// Dom style 
// let boxElement = document.querySelector(".box"); 
// console.log(boxElement.style);
// // xét kích thước cho box
// // boxElement.style.width = "100px";
// // boxElement.style.height = "100px";
// // boxElement.style.backgroundColor = "red";

// // Sử dụng Object để đỡ tốn dòng 
// Object.assign(boxElement.style, {
//     width: "100px",
//     height: "100px",
//     backgroundColor: "red"
// });
// // lấy gia giá trị width
// console.log(boxElement.style.width);

// ClassList property 
// let boxElement = document.querySelector(".box"); 
// console.log(boxElement.classList);
// // Dem so luong class 
// console.log(boxElement.classList.length);
// // lấy ra index class 
// console.log(boxElement.classList[0]);
// // Lấy ra tên class 
// console.log(boxElement.classList.value);
// // add: thêm class vào element 
// boxElement.classList.add("red","blue")  // Muốn thêm nhiều class thì phải cách nhau bằng giấu , 
// // contains: kiểm tra 1 class có nằm trong element này hay không 
// console.log(boxElement.classList.contains("red"));
// // remove: xóa 1 class khỏi element 
// console.log(boxElement.classList.remove("red"));
// // toggle: Nếu có class này ở element khi gọi tới thì sẽ xóa bỏ class đó còn nếu ko có class này ở element khi gọi tới thì sẽ thêm class đó
// console.log(boxElement.classList.toggle("blue"));
// console.log(boxElement.classList.toggle("blue"));

// Dom Event: là những hành vi diễn ra của trình duyệt hoặc là của người dùng (VD:ng dùng bôi đen, click,...)
// 1. Attribute events: Cách sử dụng để lắng nghe hành vi người dùng 
// Bên html
// 2. Assign event using the element node: gán sự kiện
// Cách 1: Chỉ lắng nghe 1 thẻ 
// let h6Element = document.querySelector("h6")  
// h6Element.onclick = function(){
//     console.log(Math.random());
// }
// // Cách 2: Lắng nghe nhiều thẻ
// let h6Element1 = document.querySelectorAll("h6")  
// Target sẽ trả lại chính element mà đang lắng nghe
// for(let i = 0;i<h6Element1.length;i++){
//     h6Element1[i].onclick = function(e){
//         console.log(e.target);
//     }
// }
// 3. Input / select 
// let inputElement = document.querySelector('input[type="type"]');
// Onchange: khi value của thẻ input này thay đổi thì mình có thể lấy ra value của nó
// inputElement.onchange = function(e){
//     console.log(e.target.value);
// }   
// // Oninput: Gõ đến đâu in ra đến đấy
// inputElement.oninput = function(e){
//     console.log(e.target.value);
// }   
// 4. Key up / down: Nó sẽ trả về mã code mà chúng ta vừa nhấn để biết mình nhấn vào phím nào để xử lý sự kiện
// Keydown
// inputElement.onkeydown = function(e){
//     console.log(e);
// }   
// inputElement.onkeyup = function(e){
//     console.log(e);
// }   

// Dom events
// 1.preventDefault: loại bỏ hành vi mặc định của trình duyệt trên 1 thẻ html 
// Lấy ra tất cả thẻ a
// let aElements = document.querySelectorAll("a") // hoặc .anchors (Nếu sử dụng anchors thì phải là có name)
// console.log(aElements);
// // Lắng nghe sự kiện click trên 2 thẻ a 
// for(let i=0;i<aElements.length;i++){
//     aElements[i].onclick = function(e){
//         // Nếu link có f8 thì mới chuyển trang còn ko có thì sẽ không chuyển 
//         if(!e.target.href.startsWith("http://f8.edu.vn")){
//             e.preventDefault();
//         }
//     }
// }
// // 2.stopPropagation: loại bỏ sự kiện nổi bọt
// document.querySelector("div").onclick = function(){
//     console.log("DIV")
// }

// document.querySelector("button").onclick = function(){
//     // Bỏ sự kiện nổi bọt (Nổi bọt là click button sẽ dính thẻ div)
//     e.stopPropagation();
//     console.log("Click me!")
// }

// Event lisener 
let btn = document.getElementById("btn");
// Lắng nghe sự kiện 
function viec1(){
    console.log("Viec 1")
}
btn.addEventListener("click",viec1)
// Hủy bỏ lắng nghe 
setTimeout(function(){
    btn.removeEventListener("click",viec1)
},3000)











