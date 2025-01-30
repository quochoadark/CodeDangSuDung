package Java.JavaArray;

import java.util.Arrays;

public class CatChuoiThanhMang {
    public static void main(String[] args) {
        String s = "Xin chao cac ban, tui la ... ";
        // Cat chuoi: split
        // Khi ra man hinh thi moi dau phay la 1 phan tu
        String a[] = s.split(" ");
        System.out.println(Arrays.toString(a));
        String b[] = s.split(",");
        System.out.println(Arrays.toString(b));
        
        String s1 = "Xin chao, minh la Peter. Minh la lap trinh vien";
        String c[] = s1.split("\\.|\\,");  // Nghia la o dau co dau phay hoac dau cham se cat ra 1 phan tu. Phai co \\ va | de ngan canh phan tu
        System.out.println(Arrays.toString(c));

        // Lay ra ten 
        String s2 = "Nguyen van A";
        String d[] = s2.split(" ");
        System.out.println("Ten: "+d[d.length-1]);
    }
}
