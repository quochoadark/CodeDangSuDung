package Java.JavaString;

public class CatNoiThayThe {
    public static void main(String[] args) {
        String s1 = "tItv";
        String s2 = ".Vn";
        String s3 = s1.concat(s2);
        // Ham concat: noi chuoi 
        System.out.println("Noi chuoi: s1 va s2: "+ s3);

        // Ham replace: thay the 
        String s4 = "Tung.vn";
        String s5 = s4.replaceAll("Tung", "TITV");
        System.out.println(s5);

        // toLowerCase: chuyen ve viet thuong 
        // toUpperCase: chuyen ve viet hoa
        
        // Ham trim(): xoa bo khoang trang du thua o dau chuoi
        String s9 = " hello";
        System.out.println(s9.trim());

        // subString: cat chuoi con 
        String s10 = "Xin chao cac ban, minh la Hoa";
        String s11 = s10.substring(9);  // tu vi tri bat dau
        System.out.println(s11);
        String s12 = s10.substring(9,16);  // (tu vi tri bat dau, vi tri ket thuc)
        System.out.println(s12);
    }
}
