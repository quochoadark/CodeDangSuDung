package vn.hoidanit.laptopshop.controller;

import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.ModelAttribute;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestMethod;

import vn.hoidanit.laptopshop.domain.User;
import vn.hoidanit.laptopshop.service.UserService;

@Controller 
public class UserController {

    private UserService userService;

    public UserController(UserService userService) {
        this.userService = userService;
    }   
    @RequestMapping("/")
    public String getHomePage(Model model){   // Model là 1 thư viện 
        String test = this.userService.handleHello();
        model.addAttribute("eric",test);   // addAttribute (khởi tạo biến, nội dung hoặc hàm)
        model.addAttribute("hoidanit","from controller with model");
        return "hello";
    }

    @RequestMapping("/admin/user")
    public String getUserPage(Model model) {
        String test = this.userService.handleHello();
        model.addAttribute("newUser", new User());
        // Đường dẫn đến thư mục đến file
        return "admin/user/create";
    }

    // Tạo 1 đường dẫn để kết nối client với sever 
    @RequestMapping(value = "/admin/user/create1", method = RequestMethod.POST)
    // Lấy ra dữ liệu của client (ModelAttribute("lấy ra tên User" User + tên))
    public String createUserPage(Model model, @ModelAttribute("newUser") User hoidanit) {   
        System.out.println("Run here" + hoidanit);  // + cho tên đã tạo ở trên
        return "hello";
    }
}

