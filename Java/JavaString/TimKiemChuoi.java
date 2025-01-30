package Java.JavaString;

public class TimKiemChuoi {
    public static void main(String[] args) {
        String s1 = "Xin chao co chu, xin chao cac ban, Xin chao!";
        String s2 = "Xin chao"; 
        String s3 = "Xin chao 123"; 
        char c1 = 'o';
        // Ham indexof(): Tim kiem chuoi 
        System.out.println("Vi tri cua s2 trong s1 la: " + s1.indexOf(s2));  // Tra ve 0 la co ton tai hoac ra so duong
        System.out.println("Vi tri cua s3 trong s1 la: " + s1.indexOf(s3));  // Tra ve -1 la ko ton tai 

        // Indexof: Tim kiem tu vi tri bat dau
        System.out.println("Vi tri cua s2 trong s1 la: " + s1.indexOf(s2,2));  // Di tu vi tri thu 2 cua s1

        // Tim kiem char
        System.out.println("Vi tri cua c1 trong s1 la: " + s1.indexOf(c1));  
        System.out.println("Vi tri cua c1 trong s1 la: " + s1.indexOf(c1,2)); 

        // Ham lastindexof: Tim kiem nguoc lai cua indexof (tu phai sang trai)
    }
}
