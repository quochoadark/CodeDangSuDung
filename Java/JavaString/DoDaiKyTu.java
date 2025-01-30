package Java.JavaString;

import java.util.Scanner;

public class DoDaiKyTu {
    public static void main(String[] args) {
        try (Scanner sc = new Scanner(System.in)){
            System.out.print("Nhap vao chuoi: ");
            String s = sc.nextLine();

            // Ham length: do dai chuoi
            System.out.println("Chuoi la: " + s.length());

            // Ham charAt(vitri) => lay ra 1 ky tu tai vi tri
            for(int i=0;i<s.length();i++){
                System.out.println("Vi tri " + i + " la: "+s.charAt(i));
            }

            // Ham getChar(Vi tri start, vi tri end, mang luu du lieu, vi tri bat dau luu cua mang): Lay hang loat ky tu 
            char[] arrayChar = new char[100];
            s.getChars(2, 4, arrayChar, 0);
            for(int i=0;i<arrayChar.length;i++){
                System.out.println("Gia tri cua mang tai " + i +" là: "+ arrayChar[i]);
            }
        } 
}
}
