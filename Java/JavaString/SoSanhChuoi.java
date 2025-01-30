package Java.JavaString;

import java.util.Scanner;

public class SoSanhChuoi {
     public static void main(String[] args) {
        try (Scanner sc = new Scanner(System.in)){
           String s1 = "titv.vn";
           String s2 = "TITV.vn";
           String s3 = "titv.vn";
           // Hàm equals: so sanh 2 chuoi giong nhau có phần biệt hoa thường 
           System.out.println("s1 và s2: "+s1.equals(s2));  // False 
           System.out.println("s2 và s3: "+s2.equals(s3));  // False 
           System.out.println("s1 và s2: "+s1.equals(s2));  // True

           // Hàm equalsIgnoreCase: so sánh 2 chuoi giong nhau ko phan biet hoa thuong
           System.out.println("s1 và s2: "+s1.equalsIgnoreCase(s2));
           System.out.println("s1 và s2: "+s1.equalsIgnoreCase(s2));

           // Hàm compareTo: So sánh chuỗi > < = phan biet hoa thuong 
           String sv1 = "Nguyễn Văn A";
           String sv2 = "Nguyễn Văn B";
           String sv3 = "Nguyễn Văn";
           String sv4 = "Nguyễn Văn A";
           System.out.println("s1 và s2: "+sv1.compareTo(sv2));  // -1 < 
           System.out.println("s1 và s3: "+sv1.compareTo(sv3));  // 2  >
           System.out.println("s1 và s4: "+sv1.compareTo(sv4));  // 0  =  

            // Hàm compareToIgnoreCase: So sánh chuỗi > < = ko phan biet hoa thuong 
            
            // Ham regionMatches: So sanh 1 doan 
            String r1 = "TITV.vn";
            String r2 = "TV.v";
            // Ham 1: (Tu ky tu cua r2, r2, tu ky cua r1, 4 ky tu cua r2)
            // Ham 2: (True or False,Tu ky tu cua r2, r2, tu ky cua r1, 4 ky tu cua r2)  // Xem la co bo qua hoa thuong hay ko
            boolean check = r1.regionMatches(2, r2, 0, 4); 
            System.out.println(check);


             // Ham startsWith: Kiem tra xem chuoi bat dau bang mot chuoi khac hoac chuoi rong  // Tra ve true or false
             String str = "titv.vn";
             System.out.println("str bat dau bang titv: "+str.startsWith("titv"));
             System.out.println("str bat dau bang rong: "+str.startsWith(""));
             // Ham endsWith: Kiem tra xem chuoi ket thuc bang mot chuoi khac hoac chuoi rong
             System.out.println("str ket thuc bang vn: "+str.endsWith("vn")); 
        } 
    }
}
