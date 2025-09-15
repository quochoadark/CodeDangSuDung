var h1Element = document.querySelector("h1");
h1Element.onclick = function (e) {  // Khi bấm vào nó sẽ hiện 
    console.log(e.target)   // target là hiện ra chính nó
}

// input / select
// key up / key down

var inputElement = document.querySelector('input[type="text"]')

// Khi người dùng thay đổi nội dung hoặc sự lựa chọn (text,select,...) thì onchange dc kich hoat

var inputValue
inputElement.onchange = function (e) {
    inputValue = e.target.value  // lấy ra giá trị
    console.log(inputValue)
}
// Gõ đến đâu lấy ra đến đó thì sử dụng oninput

// Checkbox
var checkBoxElement = document.querySelector('input[type="checkbox"]')
checkBoxElement.onchange = function (e) {
    console.log(e.target.checked)
}

// Select 
var selectElement = document.querySelector("select")
selectElement.onchange = function (e) {
    console.log(e.target.value)  // value ở đây là mình tự đặt không phải cho người dùng xem
}

// onkeyup và onkeydown sử dụng cho các phím nếu người dùng muốn ấn nhanh
// sử dụng e.which để xem số của phím đó rồi xử lý logic khi người dùng ấn vào
// Xem thêm trên youtube


// PreventDefault: loại bỏ hành vi mặc định của trình duyệt
// stopProganation: loại bỏ sự kiện nổi bọt

// EventLisener:
// addEventListener: thêm sự kiện cần lắng nghe
// => btn.addEventLisener("click",viec1) -> đầu tiên là sự kiện (bỏ chữ on) , thứ 2 là tên fuction
// removeEventListener: xóa sự kiện cần lắng nghe  -> cú pháp cũng vậy