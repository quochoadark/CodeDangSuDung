package Java.JavaArray;

import java.util.Arrays;

public class CopyMang {
        public static void main(String[] args) {
            // Kieu nguyen thuy: dung dau = (Thay ca 2 mang chinh va mang copy)

            int a[] = {1,2,3};
            // Copy mang: Dung ham clone ()
            int a1[] = a.clone();
            a1[0] = 100;
            System.out.println(Arrays.toString(a));   
            System.out.println(Arrays.toString(a1)); 

            // System.arraycopy (mang copy, vi tri bat dau cua mang copy, mang can copy, vi tri bat dau cua mang can copy, 
            // so luong phan phan can copy (lay cua mang copy))
            int a2[] = new int[a.length];
            System.arraycopy(a, 0,a2, 0, a.length);
            a2[0] = 90;
            System.out.println(Arrays.toString(a2));

            
        }
}
