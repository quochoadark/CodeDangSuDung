package Java.JavaArray;

import java.util.Arrays;

public class TimKiemSapXep {
    public static void main(String[] args) {
        int a[] = new int [] {  1, 4, 3, 2 ,5};  
        int b[] = new int [15]; 

        // Ham tim kiem: binarySearch: ra theo index phan tu 
        // luu y: mang phai tang dan 
        System.out.println(Arrays.binarySearch(a, 4));  // (Mang can tim, phan tu can tim)
        System.out.println(Arrays.binarySearch(a, -1));  // (Mang can tim, phan tu can tim)

        // Ham sap xep 
        Arrays.sort(a);
        System.out.println("Sap xep" + Arrays.toString(a));

        // Ham dien gia tri 
        Arrays.fill(b, 5);
        System.out.println(Arrays.toString(b));
    }
}
