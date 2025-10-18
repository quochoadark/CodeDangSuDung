/*
Template Name: Admin Template
Author: Wrappixel

File: js
*/
// ==============================================================
// Auto select left navbar và xử lý toggle menu
// ==============================================================
$(function () {
  "use strict";

  // --- Phần Tự động chọn (Auto select) ---
  var url = window.location.href;
  // Xóa protocol và host để so sánh URL tương đối, phòng trường hợp menu dùng URL tương đối
  var path = url.replace(
    window.location.protocol + "//" + window.location.host + "/",
    ""
  );

  // Sử dụng jQuery selector để tìm thẻ <a> có href khớp với URL hiện tại hoặc path
  var element = $("ul#sidebarnav a").filter(function () {
    return this.href === url || this.href === path;
  });

  // Thêm class 'active', 'in' và 'selected' cho menu cha
  if (element.length) {
    element.addClass("active"); // Thẻ <a> được chọn

    // Thêm class 'in' cho menu con (ul) chứa thẻ <a> được chọn
    element.closest("ul").addClass("in");

    // Thêm class 'selected' cho thẻ <li> cha của menu cha (ul) vừa được mở
    element.closest("ul").parent().addClass("selected");

    // Thêm class 'active' cho thẻ <a> cha của menu cha (ul) vừa được mở
    element.closest("ul").parent().children("a").addClass("active");
  }

  // --- Phần Xử lý sự kiện click để bung/thu gọn menu ---
  document.querySelectorAll("#sidebarnav a").forEach(function (link) {
    link.addEventListener("click", function (e) {
      const submenu = this.nextElementSibling;

      // 1. KIỂM TRA ĐIỀU KIỆN: Nếu có menu con (ul) thì CHẶN hành vi mặc định
      if (submenu && submenu.tagName === 'UL') {
        e.preventDefault();
      }

      const isActive = this.classList.contains("active");
      const parentUl = this.closest("ul");

      // Xử lý logic bung/thu gọn menu con
      if (submenu && submenu.tagName === 'UL') {
        if (!isActive) {
          // Thu gọn tất cả menu con khác cùng cấp
          parentUl.querySelectorAll("ul").forEach(function (otherSubmenu) {
            otherSubmenu.classList.remove("in");
          });
          parentUl.querySelectorAll("a").forEach(function (navLink) {
            navLink.classList.remove("active");
          });

          // Mở menu con mới
          submenu.classList.add("in");
          this.classList.add("active");
        } else {
          // Đóng menu con
          this.classList.remove("active");
          submenu.classList.remove("in");
        }
      }
    });
  });
});


